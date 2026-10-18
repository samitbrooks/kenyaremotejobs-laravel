<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => 'KRJ-TKT-'.strtoupper(Str::random(6)),
            'user_id' => null,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'category' => fake()->randomElement(['payment', 'account', 'jobs', 'employer', 'technical', 'general']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'status' => 'open',
            'subject' => fake()->sentence(5),
            'message' => fake()->paragraphs(2, true),
            'access_token' => Str::random(40),
            'last_replied_at' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status' => 'resolved',
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'closed',
        ]);
    }
}
