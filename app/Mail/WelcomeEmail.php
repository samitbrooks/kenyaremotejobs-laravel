<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Transactional, not marketing — sent once, tied directly to the user's own
// action (creating an account), so it doesn't carry an unsubscribe link the
// way App\Mail\MarketingEmail does.
class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to '.config('site.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome',
            with: ['user' => $this->user],
        );
    }
}
