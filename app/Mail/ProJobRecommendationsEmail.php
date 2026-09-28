<?php

namespace App\Mail;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class ProJobRecommendationsEmail extends Mailable implements ShouldQueue
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
        $jobCount = $this->jobs->count();

        return new Envelope(
            from: new Address(config('mail.from.address'), 'Ivy from Kenya Remote Jobs VIP Desk'),
            subject: "👑 VIP Pro Alert: {$jobCount} Real-Time Matches for You, {$firstName} (+ What We Can Do For You)",
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pro-job-recommendations',
            with: [
                'user' => $this->user,
                'firstName' => $this->user->firstName(),
                'jobs' => $this->jobs,
                'totalCount' => $this->totalCount,
                'unsubscribeUrl' => $this->unsubscribeUrl(),
                'jobsUrl' => url('/jobs'),
                'accountUrl' => url('/account'),
                'resumeBuilderUrl' => url('/resume-builder'),
                'supportEmail' => config('mail.from.address'),
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
