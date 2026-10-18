<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicketReply>
 */
class SupportTicketReplyFactory extends Factory
{
    protected $model = SupportTicketReply::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_ticket_id' => SupportTicket::factory(),
            'user_id' => null,
            'is_admin' => false,
            'sender_name' => fake()->name(),
            'message' => fake()->paragraph(),
        ];
    }

    public function admin(?User $admin = null): static
    {
        return $this->state(fn () => [
            'user_id' => $admin?->id,
            'is_admin' => true,
            'sender_name' => 'KenyaRemoteJobs Support',
        ]);
    }
}
