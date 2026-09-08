<?php

use App\Models\User;
use Livewire\Component;

new class extends Component
{
    public string $userId;

    public bool $subscribed;

    public function toggle(): void
    {
        $this->subscribed = ! $this->subscribed;
        User::where('id', $this->userId)->update([
            'subscribed' => $this->subscribed,
            'subscribed_at' => $this->subscribed ? now() : null,
        ]);
    }
};
?>

<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    class="btn-pop rounded-full px-4 py-1.5 text-xs font-semibold transition disabled:opacity-60 {{ $subscribed ? 'border border-black/10 hover:bg-black/5' : 'bg-sunrise-500 text-white hover:bg-sunrise-600' }}"
>
    {{ $subscribed ? 'Revoke full access' : 'Grant full access' }}
</button>
