<?php

namespace App\Mail;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class FreeTrialInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Collection<int, JobListing>
     */
    public Collection $sampleJobs;

    /**
     * @param  Collection<int, JobListing>|null  $sampleJobs
     */
    public function __construct(
        public User $user,
        ?Collection $sampleJobs = null
    ) {
        $this->sampleJobs = $sampleJobs ?? JobListing::query()
            ->where('kenya_friendly', true)
            ->latest('posted_at')
            ->limit(4)
            ->get();
    }

    public function envelope(): Envelope
    {
        $firstName = $this->user->firstName();

        return new Envelope(
            from: new Address(config('mail.from.address'), 'KenyaRemoteJobs'),
            subject: "{$firstName}, we found remote and/or flexible jobs you might like!",
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        $trialUrl = URL::temporarySignedRoute(
            'trial.claim',
            now()->addDays(7),
            ['user' => $this->user->id]
        );

        return new Content(
            view: 'emails.free-trial-invitation',
            with: [
                'user' => $this->user,
                'firstName' => $this->user->firstName(),
                'jobs' => $this->sampleJobs,
                'trialUrl' => $trialUrl,
                'unsubscribeUrl' => $this->unsubscribeUrl(),
            ],
        );
    }

    public function headers(): Headers
    {
        $url = $this->unsubscribeUrl();

        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$url}>",
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }

    private function unsubscribeUrl(): string
    {
        return URL::signedRoute('unsubscribe', ['user' => $this->user->id]);
    }
}
