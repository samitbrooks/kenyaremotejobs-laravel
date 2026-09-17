<?php

namespace App\Mail;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class JobMatchesDigestEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, JobListing>  $jobs
     */
    public function __construct(
        public User $user,
        public Collection $jobs,
        public int $totalCount,
    ) {}

    public function envelope(): Envelope
    {
        $firstName = $this->user->firstName();

        return new Envelope(
            from: new Address(config('mail.from.address'), 'Daisy from Kenya Remote Jobs'),
            subject: "{$firstName}, {$this->totalCount} open roles for you",
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-matches-digest',
            with: [
                'user' => $this->user,
                'firstName' => $this->user->firstName(),
                'jobs' => $this->jobs,
                'totalCount' => $this->totalCount,
                'unsubscribeUrl' => $this->unsubscribeUrl(),
                'jobsUrl' => url('/jobs'),
                'resumeBuilderUrl' => url('/resume-builder'),
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
