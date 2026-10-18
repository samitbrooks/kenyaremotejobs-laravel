<?php

namespace App\Http\Controllers;

use App\Mail\TicketReplyNotificationMail;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class AdminTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'active');
        $category = $request->query('category');
        $q = trim((string) $request->query('q', ''));

        $query = SupportTicket::query()->with('user');

        if ($q !== '') {
            $query->where(function ($b) use ($q) {
                $b->where('ticket_number', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($status === 'active') {
            $query->whereIn('status', ['open', 'in_progress']);
        } elseif (in_array($status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            $query->where('status', $status);
        }

        $tickets = $query->orderByRaw("CASE 
            WHEN priority = 'urgent' THEN 1 
            WHEN priority = 'high' THEN 2 
            WHEN priority = 'normal' THEN 3 
            ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'currentStatus' => $status,
            'currentCategory' => $category,
            'currentQuery' => $q,
            'stats' => [
                'total' => SupportTicket::count(),
                'active' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
                'open' => SupportTicket::where('status', 'open')->count(),
                'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved' => SupportTicket::where('status', 'resolved')->count(),
                'closed' => SupportTicket::where('status', 'closed')->count(),
            ],
        ]);
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['replies' => fn ($q) => $q->orderBy('created_at', 'asc'), 'user']);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(SupportTicket $ticket, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:5000'],
            'status' => ['nullable', 'string', 'in:open,in_progress,resolved,closed'],
            'send_email' => ['nullable'],
        ]);

        $reply = SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()?->id,
            'is_admin' => true,
            'sender_name' => $request->user()?->name ?: 'KenyaRemoteJobs Support',
            'message' => trim($validated['message']),
        ]);

        // Update status if passed, or default to in_progress if currently open
        if (! empty($validated['status'])) {
            $ticket->status = $validated['status'];
        } elseif ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }

        $ticket->last_replied_at = now();
        $ticket->save();

        // Optionally send customer notification email
        $shouldSendEmail = $request->boolean('send_email', true);
        if ($shouldSendEmail) {
            try {
                Mail::to($ticket->email)->send(new TicketReplyNotificationMail($ticket, $reply));
            } catch (Throwable $e) {
                Log::warning('Support ticket reply email failed to send', [
                    'ticket_number' => $ticket->ticket_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Support reply posted successfully and customer notified.');
    }

    public function updateStatus(SupportTicket $ticket, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:open,in_progress,resolved,closed'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
        ]);

        if (! empty($validated['status'])) {
            $ticket->status = $validated['status'];
        }

        if (! empty($validated['priority'])) {
            $ticket->priority = $validated['priority'];
        }

        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket metadata updated successfully.');
    }
}
