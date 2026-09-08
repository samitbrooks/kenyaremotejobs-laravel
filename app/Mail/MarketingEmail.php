<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

// Used by the admin bulk email composer — every send here carries a
// one-click unsubscribe link (both a visible footer link and the
// List-Unsubscribe / List-Unsubscribe-Post headers mailbox providers use to
// render their own "Unsubscribe" button next to the sender). Recipients are
// already filtered to exclude anyone who's opted out — see
// App\Services\BulkMailer — this header is what lets a provider act on a
// fresh unsubscribe without waiting for that filter to run again.
class MarketingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $emailSubject,
        public string $body,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.marketing',
            with: [
                'user' => $this->user,
                'body' => $this->body,
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
