<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'name',
        'email',
        'category',
        'priority',
        'status',
        'subject',
        'message',
        'access_token',
        'last_replied_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_replied_at' => 'datetime',
        ];
    }

    public static function generateTicketNumber(): string
    {
        do {
            $number = 'KRJ-TKT-'.strtoupper(Str::random(6));
        } while (static::where('ticket_number', $number)->exists());

        return $number;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class)->orderBy('created_at', 'asc');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-amber-50 text-amber-800 border-amber-300',
            'in_progress' => 'bg-[#eef0fe] text-[#3444da] border-[#c7ccfb]',
            'resolved' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
            'closed' => 'bg-slate-100 text-slate-700 border-slate-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'payment' => 'M-Pesa / Payment Issue',
            'account' => 'Account / Login Support',
            'jobs' => 'Job Application / Listing',
            'employer' => 'Employer / Hiring Inquiry',
            'technical' => 'Technical / Bug Report',
            default => 'General Inquiry',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-rose-50 text-rose-800 border-rose-300 font-bold',
            'high' => 'bg-orange-50 text-orange-800 border-orange-300',
            'normal' => 'bg-slate-100 text-slate-700 border-slate-300',
            'low' => 'bg-slate-50 text-slate-600 border-slate-200',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }
}
