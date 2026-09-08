<?php

namespace App\Console\Commands;

use App\Services\JobSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('jobs:sync')]
#[Description('Fetch and score jobs from every source, replacing the stale synced catalog')]
class SyncJobs extends Command
{
    public function handle(JobSyncService $syncService): int
    {
        $count = $syncService->sync();

        if ($count === 0) {
            $this->error('Sync skipped — every source failed or returned nothing. See logs.');

            return self::FAILURE;
        }

        $this->info("Synced {$count} jobs.");

        return self::SUCCESS;
    }
}
