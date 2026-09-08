<?php

namespace App\Services;

use App\Models\HiddenJob;
use App\Models\JobListing;
use App\Models\SyncMeta;
use App\Services\JobSources\ArbeitnowSource;
use App\Services\JobSources\GreenhouseSource;
use App\Services\JobSources\HimalayasSource;
use App\Services\JobSources\JobicySource;
use App\Services\JobSources\JobSource;
use App\Services\JobSources\RemoteOkSource;
use App\Services\JobSources\RemotiveSource;
use App\Support\Audience;
use App\Support\JobTier;
use App\Support\KenyaRelevance;
use App\Support\Mojibake;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Ported from the Next.js version's src/lib/jobsSync.ts, replacing its
 * lazy "sync on request if stale" pattern with a scheduled artisan command
 * (see App\Console\Commands\SyncJobs) — cPanel's Cron Jobs tool runs
 * `php artisan schedule:run` every minute natively, which is a better fit
 * than the request-triggered workaround the Next.js version needed.
 */
class JobSyncService
{
    /**
     * @return array<class-string, JobSource>
     */
    private function sources(): array
    {
        return [
            ArbeitnowSource::class => new ArbeitnowSource,
            RemoteOkSource::class => new RemoteOkSource,
            RemotiveSource::class => new RemotiveSource,
            JobicySource::class => new JobicySource,
            HimalayasSource::class => new HimalayasSource,
            GreenhouseSource::class => new GreenhouseSource,
        ];
    }

    public function sync(): int
    {
        $fetched = $this->fetchAllSources();
        $hiddenIds = HiddenJob::pluck('id')->flip();

        $stored = collect($fetched)
            ->reject(fn ($job) => $hiddenIds->has($job['id']))
            ->map(fn ($job) => $this->score($job))
            ->values();

        // All sources failing in the same sync (a shared network blip, not
        // several independent outages) is far more likely than upstream
        // genuinely having zero jobs — wiping every synced row would
        // otherwise erase the live catalog for a purely transient failure.
        // Skip the write and keep serving the stale-but-real data instead;
        // the next scheduled sync will retry.
        if ($stored->isEmpty()) {
            Log::warning('[job-sync] every source failed or returned nothing — skipping this sync');

            return 0;
        }

        $this->upsertSyncedJobs($stored);

        SyncMeta::where('id', 1)->update(['last_synced_at' => now()]);

        return $stored->count();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchAllSources(): array
    {
        $jobs = [];

        foreach ($this->sources() as $class => $source) {
            try {
                foreach ($source->fetch() as $job) {
                    // Keyed by id rather than pushed into an array: a
                    // paginated source (Himalayas, walked page by page while
                    // its live catalog keeps changing) can hand back the
                    // same job twice in one sync, which would otherwise hit
                    // the primary key on insert.
                    $jobs[$job['id']] = Mojibake::repairJob($job);
                }
            } catch (Throwable $e) {
                Log::warning('[job-sync] a job source failed', ['source' => $class, 'error' => $e->getMessage()]);
            }
        }

        return array_values($jobs);
    }

    /**
     * @param  array<string, mixed>  $job
     * @return array<string, mixed>
     */
    private function score(array $job): array
    {
        $relevance = KenyaRelevance::score($job);

        $job['kenya_friendly'] = $relevance['kenyaFriendly'];
        $job['kenya_score'] = $relevance['score'];
        $job['kenya_reasons'] = $relevance['reasons'];
        $job['tier'] = JobTier::compute($job['annual_salary_usd']);
        $job['audience_segments'] = Audience::classify($job);

        return $job;
    }

    /**
     * Upserts every freshly-fetched job (origin='synced'), then deletes only
     * the synced rows that didn't come back this time — a listing delisted
     * upstream disappears, same as before. Admin-authored ("manual") and
     * employer rows are never touched here, and hidden-job ids are assumed
     * to already be filtered out of $jobs by the caller so they don't
     * reappear.
     */
    private function upsertSyncedJobs(Collection $jobs): void
    {
        $keepIds = $jobs->pluck('id');

        foreach ($jobs as $job) {
            JobListing::updateOrCreate(
                ['id' => $job['id']],
                [...$job, 'origin' => 'synced', 'posted_by_user_id' => null]
            );
        }

        JobListing::where('origin', 'synced')->whereNotIn('id', $keepIds)->delete();
    }
}
