<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public SupportTicketReply $reply,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'KenyaRemoteJobs Support'),
            replyTo: [config('mail.from.address')],
            subject: "[{$this->ticket->ticket_number}] {$this->ticket->subject}",
        );
    }

    public function content(): Content
    {
        $ticketUrl = route('support.show', [
            'ticketNumber' => $this->ticket->ticket_number,
            'token' => $this->ticket->access_token,
        ]);

        return new Content(
            markdown: 'emails.ticket-reply',
            with: [
                'ticket' => $this->ticket,
                'reply' => $this->reply,
                'ticketUrl' => $ticketUrl,
            ],
        );
    }
}
