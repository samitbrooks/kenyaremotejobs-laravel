<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PricingCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public static function billingPlans(): array
    {
        return [
            'monthly' => ['pro', 250, '1 month'],
            'monthly alias' => ['monthly', 250, '1 month'],
            'quarterly' => ['quarterly', 499, '3 months'],
            'yearly' => ['yearly', 899, '12 months'],
        ];
    }

    #[DataProvider('billingPlans')]
    public function test_selected_plan_reaches_checkout_with_the_advertised_price(string $plan, int $price, string $duration): void
    {
        $user = User::factory()->create();
        config(['payments.default' => 'mpesa']);
        $this->mock(PaymentGateway::class)->shouldReceive('initiate')->once();

        $response = $this->actingAs($user)->get(route('pricing', ['plan' => $plan]));

        $response->assertViewHas('selectedPlan', $plan)
            ->assertSee('Get Pro for '.$duration.' (KES '.$price.')')
            ->assertSee('"period":"'.$plan.'"');
        $response->assertSeeLivewire('subscribe-button');

        Livewire::test('subscribe-button', ['period' => $response->viewData('selectedPlan')])
            ->call('promptCheckout')
            ->assertSee('Pay KES '.$price.' with M-Pesa')
            ->set('phone', '0712345678')
            ->call('subscribe')
            ->assertSee('Check your phone');

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'purpose' => 'subscription',
            'amount_kes' => $price,
            'status' => 'pending',
        ]);
        $this->assertSame($plan, Payment::sole()->payload['period']);
    }

    public static function invalidPlans(): array
    {
        return [
            'unknown' => ['unknown'],
            'legacy tier' => ['starter'],
            'array' => [['quarterly']],
        ];
    }

    #[DataProvider('invalidPlans')]
    public function test_invalid_plan_falls_back_to_monthly_checkout(string|array $plan): void
    {
        $this->get(route('pricing', ['plan' => $plan]))
            ->assertViewHas('selectedPlan', 'pro')
            ->assertSee('Get Pro for 1 month (KES 250)');
    }

    public function test_pricing_defaults_to_monthly(): void
    {
        $this->get(route('pricing'))
            ->assertViewHas('selectedPlan', 'pro')
            ->assertSee('"period":"pro"');
    }

    public function test_guest_checkout_preserves_selected_plan_through_login(): void
    {
        Livewire::test('subscribe-button', ['period' => 'yearly'])
            ->call('promptCheckout')
            ->assertRedirect('/account?next=/pricing?plan=yearly');

        $this->get('/account?next=/pricing?plan=yearly')
            ->assertViewHas('redirectTo', '/pricing?plan=yearly');
        $this->assertDatabaseCount('payments', 0);
    }
}
