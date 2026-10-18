<x-mail::message>
# Hello {{ $ticket->name }},

Our support team has posted an update regarding your support ticket:

**Ticket Reference:** {{ $ticket->ticket_number }}  
**Subject:** {{ $ticket->subject }}  
**Status:** {{ $ticket->statusLabel() }}

---

### Update from {{ $reply->sender_name }}:

{!! nl2br(e($reply->message)) !!}

---

<x-mail::button :url="$ticketUrl">
View Ticket &amp; Reply Online
</x-mail::button>

If you have additional questions or follow-up details, please click the button above to respond directly on our secure support desk.

Best regards,  
**KenyaRemoteJobs Support Team**  
*Nairobi, Kenya &bull; East Africa Time (UTC+3)*
</x-mail::message>
