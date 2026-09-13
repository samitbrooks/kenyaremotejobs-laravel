<?php

use App\Models\JobApplication;
use Livewire\Component;

new class extends Component
{
    public string $jobId;

    public bool $isAuthed = false;

    public ?int $applicationId = null;

    public string $status = 'applied';

    public string $notes = '';

    public string $recruiterContact = '';

    public bool $showNotesModal = false;

    public bool $savedSuccess = false;

    public function mount(string $jobId): void
    {
        $this->jobId = $jobId;
        $user = auth()->user();
        $this->isAuthed = (bool) $user;

        if ($user) {
            $app = JobApplication::where('user_id', $user->id)
                ->where('job_listing_id', $jobId)
                ->first();

            if ($app) {
                $this->applicationId = $app->id;
                $this->status = $app->status;
                $this->notes = $app->notes ?? '';
                $this->recruiterContact = $app->recruiter_contact ?? '';
            }
        }
    }

    public function toggleTrack(): void
    {
        if (! $this->isAuthed) {
            $this->redirect('/account?next=/jobs/'.$this->jobId, navigate: false);

            return;
        }

        $user = auth()->user();

        if ($this->applicationId) {
            // Already tracked, open edit notes or cycle status
            $this->showNotesModal = true;

            return;
        }

        $app = JobApplication::create([
            'user_id' => $user->id,
            'job_listing_id' => $this->jobId,
            'status' => 'applied',
            'applied_at' => now(),
        ]);

        $this->applicationId = $app->id;
        $this->status = 'applied';
        $this->savedSuccess = true;
    }

    public function updateStatus(string $newStatus): void
    {
        if (! $this->applicationId || ! auth()->check()) {
            return;
        }

        $valid = ['saved', 'applied', 'interviewing', 'offered', 'rejected'];
        if (! in_array($newStatus, $valid)) {
            return;
        }

        $this->status = $newStatus;
        JobApplication::where('id', $this->applicationId)
            ->where('user_id', auth()->id())
            ->update([
                'status' => $newStatus,
                'applied_at' => $newStatus === 'applied' ? now() : null,
            ]);

        $this->savedSuccess = true;
    }

    public function saveNotes(): void
    {
        if (! $this->applicationId || ! auth()->check()) {
            return;
        }

        JobApplication::where('id', $this->applicationId)
            ->where('user_id', auth()->id())
            ->update([
                'notes' => $this->notes,
                'recruiter_contact' => $this->recruiterContact,
            ]);

        $this->showNotesModal = false;
        $this->savedSuccess = true;
    }

    public function removeTracking(): void
    {
        if (! $this->applicationId || ! auth()->check()) {
            return;
        }

        JobApplication::where('id', $this->applicationId)
            ->where('user_id', auth()->id())
            ->delete();

        $this->applicationId = null;
        $this->status = 'applied';
        $this->notes = '';
        $this->recruiterContact = '';
        $this->showNotesModal = false;
        $this->savedSuccess = false;
    }
};
?>

<div class="relative inline-block text-left">
    @if ($applicationId)
        <div class="inline-flex items-center rounded-xl border border-emerald-300 bg-emerald-50 p-1 shadow-sm">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-emerald-800">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Tracked:
            </span>
            <select
                wire:change="updateStatus($event.target.value)"
                class="rounded-lg border-none bg-white px-2.5 py-1 text-xs font-semibold text-emerald-900 shadow-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
            >
                <option value="applied" @selected($status === 'applied')>Applied</option>
                <option value="interviewing" @selected($status === 'interviewing')>Interviewing 🎯</option>
                <option value="offered" @selected($status === 'offered')>Offer Received 🎉</option>
                <option value="saved" @selected($status === 'saved')>Saved / Drafting</option>
                <option value="rejected" @selected($status === 'rejected')>Archived</option>
            </select>
            <button
                type="button"
                wire:click="$toggle('showNotesModal')"
                title="Application Notes & Recruiter Info"
                class="ml-1 rounded-lg px-2 py-1 text-xs font-medium text-emerald-700 hover:bg-emerald-100 transition"
            >
                ✏️ Notes
            </button>
        </div>
    @else
        <button
            type="button"
            wire:click="toggleTrack"
            class="inline-flex items-center gap-1.5 rounded-xl border border-black/10 bg-white px-3.5 py-2 text-xs font-semibold text-foreground/80 shadow-xs hover:border-sunrise-300 hover:bg-sunrise-50/50 hover:text-sunrise-700 transition"
        >
            <svg class="h-4 w-4 text-sunrise-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            + Track in My Applications
        </button>
    @endif

    {{-- Notes & Recruiter CRM Modal --}}
    @if ($showNotesModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-black/5">
                    <h3 class="font-bold text-foreground">Application CRM Notes</h3>
                    <button type="button" wire:click="$set('showNotesModal', false)" class="text-foreground/40 hover:text-foreground">
                        ✕
                    </button>
                </div>

                <div class="mt-4 space-y-4 text-left">
                    <div>
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Recruiter Name / Email / LinkedIn</label>
                        <input
                            type="text"
                            wire:model="recruiterContact"
                            placeholder="e.g. Sarah Connor (Talent Lead) - sarah@company.com"
                            class="w-full rounded-xl border border-black/10 px-3 py-2 text-sm focus:border-sunrise-500 focus:outline-none focus:ring-1 focus:ring-sunrise-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Interview Notes / Salary Discussed / Follow-up Date</label>
                        <textarea
                            wire:model="notes"
                            rows="4"
                            placeholder="e.g. Applied via early access. Screening call scheduled for Thursday 4PM EAT. Stated salary range: $3,500/mo."
                            class="w-full rounded-xl border border-black/10 px-3 py-2 text-sm focus:border-sunrise-500 focus:outline-none focus:ring-1 focus:ring-sunrise-500"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between pt-3 border-t border-black/5">
                    <button
                        type="button"
                        wire:click="removeTracking"
                        wire:confirm="Remove this job from your tracked applications?"
                        class="text-xs text-red-600 hover:underline"
                    >
                        Untrack Job
                    </button>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            wire:click="$set('showNotesModal', false)"
                            class="rounded-xl border border-black/10 px-4 py-2 text-xs font-semibold text-foreground/70 hover:bg-black/5"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            wire:click="saveNotes"
                            class="rounded-xl bg-sunrise-500 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-sunrise-600"
                        >
                            Save Notes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
