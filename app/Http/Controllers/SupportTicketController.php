<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();

        return view('support.index', [
            'prefillName' => $user?->name ?? old('name', ''),
            'prefillEmail' => $user?->email ?? old('email', ''),
            'selectedCategory' => $request->query('category', old('category', 'general')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Simple honeypot anti-spam check
        if ($request->filled('website_hp')) {
            return redirect()->route('support.index');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'category' => ['required', 'string', 'in:payment,account,jobs,employer,technical,general'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'subject' => ['required', 'string', 'min:4', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $request->user()?->id,
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'category' => $validated['category'],
            'priority' => $validated['priority'] ?? 'normal',
            'status' => 'open',
            'subject' => trim($validated['subject']),
            'message' => trim($validated['message']),
            'access_token' => Str::random(40),
            'last_replied_at' => now(),
        ]);

        // Store access token in session for seamless subsequent access
        session()->put("ticket_token_{$ticket->ticket_number}", $ticket->access_token);

        return redirect()->route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ])->with('success', "Ticket {$ticket->ticket_number} created successfully! Our team will respond shortly.");
    }

    public function show(string $ticketNumber, Request $request): View|RedirectResponse
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        if (! $this->isAuthorized($ticket, $request)) {
            return redirect()->route('support.index')->with('error', 'Access link expired or invalid ticket credentials.');
        }

        // Keep access token in session
        if ($request->query('token') === $ticket->access_token) {
            session()->put("ticket_token_{$ticket->ticket_number}", $ticket->access_token);
        }

        $ticket->load(['replies' => fn ($q) => $q->orderBy('created_at', 'asc'), 'user']);

        return view('support.show', [
            'ticket' => $ticket,
            'accessToken' => $ticket->access_token,
        ]);
    }

    public function reply(string $ticketNumber, Request $request): RedirectResponse
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        if (! $this->isAuthorized($ticket, $request)) {
            return redirect()->route('support.index')->with('error', 'Access link expired or invalid ticket credentials.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $senderName = $request->user()?->name ?? $ticket->name;

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()?->id,
            'is_admin' => false,
            'sender_name' => $senderName,
            'message' => trim($validated['message']),
        ]);

        // Re-open ticket if it was resolved or closed
        if (in_array($ticket->status, ['resolved', 'closed'], true)) {
            $ticket->status = 'open';
        }

        $ticket->last_replied_at = now();
        $ticket->save();

        return redirect()->route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ])->with('success', 'Your follow-up message has been added to the ticket.');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $ticket = SupportTicket::where('ticket_number', strtoupper(trim($validated['ticket_number'])))
            ->where('email', strtolower(trim($validated['email'])))
            ->first();

        if (! $ticket) {
            return redirect()->route('support.index')->with('lookup_error', 'No ticket found with that reference number and email.');
        }

        session()->put("ticket_token_{$ticket->ticket_number}", $ticket->access_token);

        return redirect()->route('support.show', [
            'ticketNumber' => $ticket->ticket_number,
            'token' => $ticket->access_token,
        ]);
    }

    private function isAuthorized(SupportTicket $ticket, Request $request): bool
    {
        // Check if current user owns the ticket
        if ($request->user() && $ticket->user_id === $request->user()->id) {
            return true;
        }

        // Check if admin user
        if ($request->user() && in_array($request->user()->email, config('jobs.admin_emails', []), true)) {
            return true;
        }

        // Check token from query param
        if ($request->query('token') && hash_equals($ticket->access_token, (string) $request->query('token'))) {
            return true;
        }

        // Check session token
        $sessionToken = session()->get("ticket_token_{$ticket->ticket_number}");
        if ($sessionToken && hash_equals($ticket->access_token, (string) $sessionToken)) {
            return true;
        }

        return false;
    }
}
