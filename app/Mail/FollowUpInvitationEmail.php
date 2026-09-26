<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class FollowUpInvitationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'KenyaRemoteJobs'),
            subject: 'Still thinking about finding a remote job?',
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.follow-up-membership',
            with: [
                'user' => $this->user,
                'firstName' => $this->user->firstName(),
                'pricingUrl' => url('/pricing'),
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
