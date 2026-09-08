<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// The same link serves two purposes depending on $isNewAccount: confirming
// a brand-new signup, or logging an existing user back in. Both are
// "click to access your account" — there's no separate password to check.
class LoginLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $url,
        public bool $isNewAccount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isNewAccount
                ? 'Confirm your '.config('site.name').' account'
                : 'Your '.config('site.name').' login link',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.login-link',
            with: [
                'user' => $this->user,
                'url' => $this->url,
                'isNewAccount' => $this->isNewAccount,
            ],
        );
    }
}
