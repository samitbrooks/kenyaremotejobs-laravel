<?php

namespace App\Livewire\Concerns;

/**
 * Enforces administrative access on Livewire / Volt components.
 *
 * Livewire components handle user interactions via POST requests directly to
 * /livewire/update outside the parent route's middleware group. This trait
 * hooks into Livewire's component lifecycle to verify that the authenticated
 * user is an authorized administrator before any action or property update runs.
 */
trait RequiresAdmin
{
    public function bootRequiresAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Unauthorized administrative action.');
    }
}
