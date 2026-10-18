<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_visiting_referrals(): void
    {
        $response = $this->get('/referrals');
        $response->assertRedirect('/account?next=%2Freferrals');
        $response->assertSessionHas('info');
    }

    public function test_authenticated_user_can_view_referrals_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'jane.doe@example.com',
            'name' => 'Jane Doe',
            'free_tailors_remaining' => 1,
        ]);

        $response = $this->actingAs($user)->get('/referrals');
        $response->assertOk();
        $response->assertSee('Refer Friends,');
        $response->assertSee('Earn Free Pro Access');
        $response->assertSee($user->referralCode());
        $response->assertSee('Share on WhatsApp');
        $response->assertSee('Your Referred Network');
    }

    public function test_capture_referral_code_middleware_stores_cookie_and_session(): void
    {
        $referrer = User::factory()->create(['email' => 'ref.owner@example.com']);
        $code = $referrer->referralCode();

        $response = $this->get("/?ref={$code}");
        $response->assertOk();
        $response->assertCookie(ReferralService::COOKIE_NAME, $code);
        $this->assertEquals($code, session(ReferralService::COOKIE_NAME));
    }

    public function test_new_user_signup_with_referral_awards_ai_tailor_credits_to_both(): void
    {
        $referrer = User::factory()->create([
            'email' => 'referrer@example.com',
            'free_tailors_remaining' => 1,
        ]);
        $code = $referrer->referralCode();

        // New user registers with referral cookie set
        $response = $this->withCookie(ReferralService::COOKIE_NAME, $code)
            ->post('/account/login', [
                'email' => 'friend@example.com',
                'name' => 'Friend Candidate',
            ]);

        $response->assertRedirect('/account');

        $newUser = User::where('email', 'friend@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals($referrer->id, $newUser->referred_by_id);

        // Verify rewards applied
        $referrer->refresh();
        $this->assertEquals(3, $referrer->free_tailors_remaining); // 1 + 2
        $this->assertEquals(3, $newUser->free_tailors_remaining); // 1 initial default + 2 referral bonus

        $referral = Referral::where('referred_id', $newUser->id)->first();
        $this->assertNotNull($referral);
        $this->assertEquals($referrer->id, $referral->referrer_id);
        $this->assertEquals('signed_up', $referral->status);
        $this->assertTrue($referral->signup_reward_applied);
        $this->assertFalse($referral->trial_reward_applied);
    }

    public function test_free_trial_activation_awards_referrer_3_days_pro_access(): void
    {
        $referrer = User::factory()->create([
            'email' => 'referrer@example.com',
            'free_tailors_remaining' => 1,
            'pro_access_until' => null,
            'subscribed' => false,
        ]);
        $code = $referrer->referralCode();

        $newUser = User::factory()->create([
            'email' => 'friend@example.com',
            'referred_by_id' => $referrer->id,
        ]);

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'status' => 'signed_up',
            'signup_reward_applied' => true,
        ]);

        $this->assertFalse($referrer->hasReferralProAccess());
        $this->assertFalse($referrer->hasActiveAccess());

        // New user activates free trial
        $response = $this->actingAs($newUser)->post('/trial/activate');
        $response->assertRedirect('/jobs');

        $referrer->refresh();
        $this->assertTrue($referrer->hasReferralProAccess());
        $this->assertTrue($referrer->hasActiveAccess());
        $this->assertNotNull($referrer->pro_access_until);
        $this->assertGreaterThan(now()->addDays(2), $referrer->pro_access_until);

        $referral = Referral::where('referred_id', $newUser->id)->first();
        $this->assertEquals('trial_activated', $referral->status);
        $this->assertTrue($referral->trial_reward_applied);
    }

    public function test_subscription_purchase_awards_referrer_14_days_pro_access(): void
    {
        $referrer = User::factory()->create([
            'email' => 'referrer@example.com',
            'free_tailors_remaining' => 2,
            'pro_access_until' => null,
            'subscribed' => false,
        ]);

        $subscriber = User::factory()->create([
            'email' => 'subbing.friend@example.com',
            'referred_by_id' => $referrer->id,
        ]);

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $subscriber->id,
            'status' => 'signed_up',
            'signup_reward_applied' => true,
            'trial_reward_applied' => false,
            'conversion_reward_applied' => false,
        ]);

        // Purchase and fulfill subscription
        $paymentService = app(PaymentService::class);
        $payment = $paymentService->subscribe($subscriber, 'pro');

        $referrer->refresh();
        // Referrer receives +14 days Pro access
        $this->assertTrue($referrer->hasReferralProAccess());
        $this->assertTrue($referrer->hasActiveAccess());
        $this->assertGreaterThan(now()->addDays(13), $referrer->pro_access_until);
        $this->assertEquals(7, $referrer->free_tailors_remaining); // 2 + 5

        $referral = Referral::where('referred_id', $subscriber->id)->first();
        $this->assertEquals('converted', $referral->status);
        $this->assertTrue($referral->conversion_reward_applied);
    }

    public function test_referrals_page_renders_stats_and_masked_emails_accurately(): void
    {
        $referrer = User::factory()->create([
            'email' => 'referrer.boss@example.com',
        ]);

        $friend = User::factory()->create([
            'name' => 'Brian Mwangi',
            'email' => 'brian.mwangi@gmail.com',
            'referred_by_id' => $referrer->id,
        ]);

        Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $friend->id,
            'status' => 'converted',
            'signup_reward_applied' => true,
            'trial_reward_applied' => true,
            'conversion_reward_applied' => true,
        ]);

        $response = $this->actingAs($referrer)->get('/referrals');
        $response->assertOk();
        $response->assertSee('Brian');
        // Masked email must be shown, not full raw email
        $response->assertSee('br***@gmail.com');
        $response->assertDontSee('brian.mwangi@gmail.com');
        $response->assertSee('+17'); // 3 + 14 days
    }
}
