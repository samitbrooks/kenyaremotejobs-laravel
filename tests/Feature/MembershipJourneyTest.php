<?php

namespace Tests\Feature;

use App\Mail\WelcomeEmail;
use App\Models\JobListing;
use App\Models\Payment;
use App\Models\User;
use App\Payments\PaymentGateway;
use App\Services\AiTailorService;
use App\Services\JobRecommendationService;
use App\Support\Matching;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class MembershipJourneyTest extends TestCase
{
    use RefreshDatabase;

    private function job(string $id = 'journey-role', array $attributes = []): JobListing
    {
        return JobListing::create(array_merge([
            'id' => $id, 'source_name' => 'Internal', 'source_id' => $id,
            'source_url' => 'https://hidden-employer.example/apply', 'title' => 'Python Developer',
            'company' => 'Hidden Employer Ltd', 'description' => 'Python role with flexible hours.',
            'tags' => ['Python'], 'location' => 'Worldwide', 'remote_type' => 'Remote',
            'posted_at' => now(), 'kenya_friendly' => true, 'kenya_score' => 3,
            'kenya_reasons' => [], 'origin' => 'synced', 'tier' => 'basic', 'audience_segments' => [],
        ], $attributes));
    }

    public function test_signed_in_match_preferences_are_saved_and_used_without_browser_cookie(): void
    {
        $user = User::factory()->create();
        $this->job();

        Livewire::actingAs($user)->test('match-form')
            ->set('skills', ['Python'])->set('yearsExperience', 2)
            ->call('save')->assertRedirect('/jobs?sort=match');

        $this->assertSame(['Python'], $user->fresh()->job_search_profile['skills']);
        $this->get('/jobs?sort=match')->assertViewHas('hasProfile', true)->assertViewHas('sortByMatch', true);
    }

    public function test_empty_match_selection_is_rejected_without_saving(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('match-form')->call('save')
            ->assertHasErrors('skills')->assertSee('Choose at least one skill or role');

        $this->assertNull($user->fresh()->job_search_profile);
    }

    public function test_invalid_match_values_are_rejected_without_saving(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('match-form')->set('skills', ['not-an-option'])
            ->set('roles', ['invalid'])->set('yearsExperience', -1)->call('save')
            ->assertHasErrors(['skills.0', 'roles.0', 'yearsExperience']);

        $this->assertNull($user->fresh()->job_search_profile);
    }

    public function test_clearing_preferences_also_clears_account_profile(): void
    {
        $user = User::factory()->create(['job_search_profile' => ['skills' => ['Python'], 'roles' => [], 'yearsExperience' => 2]]);

        Livewire::actingAs($user)->test('match-form')->call('clear')->assertSet('hasProfile', false);

        $this->assertNull($user->fresh()->job_search_profile);
    }

    public function test_guest_matching_does_not_require_membership(): void
    {
        Livewire::test('match-form')->set('skills', ['Python'])->call('save')
            ->assertRedirect('/jobs?sort=match');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_recommendations_include_relevant_current_roles_and_exclude_expired_or_unrelated_roles(): void
    {
        $this->freezeTime();
        $user = User::factory()->create(['job_search_profile' => ['skills' => ['Python'], 'roles' => [], 'yearsExperience' => 2]]);
        $this->job('relevant');
        $this->job('expired', ['posted_at' => now()->subDays(31)]);
        $this->job('unrelated', ['title' => 'Accountant', 'description' => 'Bookkeeping role', 'tags' => ['Accounting']]);

        $service = app(JobRecommendationService::class);

        $this->assertSame(['relevant'], $service->getRecommendedJobsForUser($user)->modelKeys());
        $this->assertSame(['relevant'], $service->getProRecommendedJobsForUser($user)->modelKeys());
        $this->assertSame(2, $service->getTotalOpenRolesCount());
    }

    public function test_empty_catalog_reports_zero_roles(): void
    {
        $this->assertSame(0, app(JobRecommendationService::class)->getTotalOpenRolesCount());
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_email_preference_updates_only_the_signed_in_user(bool $enabled): void
    {
        $user = User::factory()->create(['marketing_opt_out_at' => now()]);
        $other = User::factory()->create(['marketing_opt_out_at' => now()]);

        Livewire::actingAs($user)->test('alert-preferences')->set('emailAlerts', $enabled)->call('save')
            ->assertSee('Your email preference has been saved.');

        $this->assertSame($enabled, $user->fresh()->receivesMarketingEmail());
        $this->assertFalse($other->fresh()->receivesMarketingEmail());
    }

    public function test_guest_cannot_save_alert_preferences(): void
    {
        Livewire::test('alert-preferences')->set('emailAlerts', true)->call('save')->assertForbidden();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_active_trial_has_cv_access_without_spending_free_credit_and_expired_trial_does_not(): void
    {
        $this->freezeTime();
        $user = User::factory()->create(['trial_started_at' => now(), 'trial_ends_at' => now()->addDay(), 'free_tailors_remaining' => 0]);

        $this->assertTrue($user->canUseAiTailor());
        $user->useAiTailor();
        $this->assertSame(0, $user->fresh()->free_tailors_remaining);
        $this->travel(25)->hours();
        $this->assertFalse($user->fresh()->canUseAiTailor());
    }

    public function test_trial_returns_to_selected_job_and_rejects_external_redirect(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/trial/activate?next=%2Fjobs%2Fchosen-role')->assertRedirect('/jobs/chosen-role');
        $this->get('/trial/activate?next=https://example.com')->assertRedirect('/jobs');
        $this->assertTrue($user->fresh()->onTrial());
    }

    public function test_new_tracker_entry_is_a_shortlist_not_a_claimed_application(): void
    {
        $user = User::factory()->create();
        $job = $this->job();

        Livewire::actingAs($user)->test('track-application-button', ['jobId' => $job->id])
            ->call('toggleTrack')->assertSet('status', 'saved');

        $this->assertDatabaseHas('job_applications', ['user_id' => $user->id, 'job_listing_id' => $job->id, 'status' => 'saved', 'applied_at' => null]);
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function test_new_eligibility_panel_preserves_employer_masking_for_guests_and_members(bool $subscribed): void
    {
        $job = $this->job(attributes: ['location' => 'Worldwide at Hidden Employer Ltd']);
        if ($subscribed) {
            $this->actingAs(User::factory()->create(['subscribed' => true]));
        }

        $response = $this->get('/jobs/'.$job->id);

        $response->assertSee('Could this role work from Kenya?')->assertSee('Potential Kenya fit')
            ->assertDontSee('Hidden Employer Ltd');
        if (! $subscribed) {
            $response->assertDontSee('https://hidden-employer.example/apply');
        }
    }

    public function test_location_restrictions_are_shown_as_uncertain_even_if_stored_badge_says_friendly(): void
    {
        $job = $this->job(attributes: ['location' => 'US only', 'description' => 'Must reside in the US.']);

        $this->get('/jobs/'.$job->id)->assertSee('Kenya eligibility needs confirmation')->assertSee('Restriction detected');
    }

    public function test_account_shows_seven_day_plan_and_keeps_tracked_employer_hidden(): void
    {
        $user = User::factory()->create();
        $job = $this->job();
        $user->jobApplications()->create(['job_listing_id' => $job->id, 'status' => 'saved']);

        $this->actingAs($user)->get('/account')->assertSee('Your seven-day application plan')
            ->assertSee('Plan your next week')->assertSee('Done in your account')->assertDontSee('Hidden Employer Ltd');
    }

    public function test_pending_checkout_cannot_initiate_another_charge(): void
    {
        $user = User::factory()->create();
        $payment = Payment::create(['user_id' => $user->id, 'gateway' => 'mpesa', 'purpose' => 'subscription', 'payload' => ['period' => 'pro'], 'amount_kes' => 250, 'status' => 'pending']);
        $this->mock(PaymentGateway::class)->shouldNotReceive('initiate');

        Livewire::actingAs($user)->test('subscribe-button')->set('pendingPaymentId', $payment->id)
            ->call('subscribe')->assertSee('Check payment status');

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_failed_payment_restores_retry_and_cannot_read_another_users_payment(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $payment = Payment::create(['user_id' => $other->id, 'gateway' => 'mpesa', 'purpose' => 'subscription', 'payload' => [], 'amount_kes' => 250, 'status' => 'completed']);

        Livewire::actingAs($user)->test('subscribe-button')->set('pendingPaymentId', $payment->id)
            ->call('checkPaymentStatus')->assertSet('pendingPaymentId', null)
            ->assertSee('Please try again.')->assertNoRedirect();
    }

    public function test_cv_trial_workflow_masks_employer_in_generated_output(): void
    {
        $user = User::factory()->create(['trial_started_at' => now(), 'trial_ends_at' => now()->addDay(), 'free_tailors_remaining' => 0]);
        $job = $this->job();
        $this->mock(AiTailorService::class)->shouldReceive('tailor')->once()->andReturn([
            'match_score' => 94, 'key_requirements' => ['Python'],
            'tailored_bullets' => ['Work at Hidden Employer Ltd'],
            'tailored_cover_letter' => 'Dear Hidden Employer Ltd team', 'kenya_advantage' => 'UTC+3',
        ]);

        Livewire::actingAs($user)->test('ai-tailor-modal', ['jobId' => $job->id])
            ->call('open')->assertSee('Draft for your review')->assertDontSee('Hidden Employer Ltd');

        $this->assertSame(0, $user->fresh()->free_tailors_remaining);
    }

    public function test_completed_payment_opens_the_members_application_plan(): void
    {
        $user = User::factory()->create();
        config(['payments.default' => 'mpesa']);
        $this->mock(PaymentGateway::class)->shouldReceive('initiate')->once()
            ->andReturnUsing(function (Payment $payment): void {
                $payment->update(['status' => 'completed', 'completed_at' => now()]);
            });

        Livewire::actingAs($user)->test('subscribe-button')->set('phone', '0712345678')
            ->call('subscribe')->assertRedirect('/account#search-plan');

        $this->assertTrue($user->fresh()->subscribed);
        $this->assertDatabaseHas('payments', ['user_id' => $user->id, 'status' => 'completed', 'amount_kes' => 250]);
    }

    public function test_login_preserves_guest_matching_choices_for_future_alerts(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('auth.link', now()->addMinutes(10), ['user' => $user->id]);
        $profile = ['skills' => ['Python'], 'roles' => [], 'yearsExperience' => 2];

        $this->withCookie(Matching::COOKIE_NAME, json_encode($profile))->get($url)->assertRedirect('/account');

        $this->assertSame($profile, $user->fresh()->job_search_profile);
        Mail::assertSent(WelcomeEmail::class);
    }
}
