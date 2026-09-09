<?php

namespace App\Services;

use App\Models\CreditPurchase;
use App\Models\JobListing;
use App\Models\JobPostingPayment;
use App\Models\Payment;
use App\Models\User;
use App\Payments\PaymentGateway;
use App\Support\Audience;
use App\Support\KenyaRelevance;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Every purchase in the app — credit packages, subscriptions, employer job
 * postings — creates a Payment row, hands it to the configured
 * PaymentGateway to initiate, and (once the gateway marks it completed)
 * runs the purpose-specific fulfillment exactly once. MockGateway resolves
 * that synchronously today; a real gateway resolves it later from a webhook
 * by calling fulfill() again with the same Payment.
 *
 * Ported from the Next.js version's creditsRepo.ts/employerRepo.ts/
 * subscription route, whose TODO(payments) comments described exactly this
 * shape ("only call this from the provider's payment-confirmed webhook").
 */
class PaymentService
{
    public function __construct(private PaymentGateway $gateway) {}

    public function purchaseCreditPackage(User $user, string $tier, ?string $phone = null): Payment
    {
        if (! in_array($tier, config('jobs.tiers'), true)) {
            throw new InvalidArgumentException("Invalid tier: {$tier}");
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => config('payments.default'),
            'purpose' => 'credit_package',
            'payload' => ['tier' => $tier],
            'amount_kes' => config('jobs.credit_packages')[$tier]['price_kes'],
            'phone' => $phone,
            'status' => 'pending',
        ]);

        return $this->initiateAndMaybeFulfill($payment);
    }

    public function subscribe(User $user, string $period, ?string $phone = null): Payment
    {
        $plans = config('jobs.subscription_plans');
        if (! array_key_exists($period, $plans)) {
            throw new InvalidArgumentException("Invalid billing period: {$period}");
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => config('payments.default'),
            'purpose' => 'subscription',
            'payload' => ['period' => $period],
            'amount_kes' => $plans[$period]['price_kes'],
            'phone' => $phone,
            'status' => 'pending',
        ]);

        return $this->initiateAndMaybeFulfill($payment);
    }

    /**
     * @param  array{title: string, company: string, description: string, tags: array<int, string>, location: string, remote_type: string, salary: ?string, source_url: string}  $jobInput
     */
    public function postEmployerJob(User $user, array $jobInput, string $plan, ?string $phone = null): Payment
    {
        $plans = config('jobs.posting_plans');
        if (! array_key_exists($plan, $plans)) {
            throw new InvalidArgumentException("Invalid posting plan: {$plan}");
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => config('payments.default'),
            'purpose' => 'employer_job_post',
            'payload' => ['job' => $jobInput, 'plan' => $plan],
            'amount_kes' => $plans[$plan]['price_kes'],
            'phone' => $phone,
            'status' => 'pending',
        ]);

        return $this->initiateAndMaybeFulfill($payment);
    }

    private function initiateAndMaybeFulfill(Payment $payment): Payment
    {
        $this->gateway->initiate($payment);
        $payment->refresh();

        if ($payment->isCompleted()) {
            $this->fulfill($payment);
        }

        return $payment;
    }

    /**
     * Runs the purpose-specific side effect for a confirmed payment.
     * Idempotent by design (a webhook can retry delivery) — CreditPurchase/
     * JobPostingPayment rows aren't re-created for a payment that already
     * has one, and re-subscribing just refreshes subscribed_at.
     */
    public function fulfill(Payment $payment): void
    {
        if (! $payment->isCompleted()) {
            return;
        }

        match ($payment->purpose) {
            'credit_package' => $this->fulfillCreditPackage($payment),
            'subscription' => $this->fulfillSubscription($payment),
            'employer_job_post' => $this->fulfillEmployerJobPost($payment),
            default => null,
        };
    }

    private function fulfillCreditPackage(Payment $payment): void
    {
        if (CreditPurchase::where('id', $payment->id)->exists()) {
            return;
        }

        $tier = $payment->payload['tier'];
        $pkg = config('jobs.credit_packages')[$tier];

        CreditPurchase::create([
            'id' => $payment->id,
            'user_id' => $payment->user_id,
            'tier' => $tier,
            'credits' => $pkg['credits'],
            'amount_kes' => $pkg['price_kes'],
            'purchased_at' => $payment->completed_at,
        ]);
    }

    private function fulfillSubscription(Payment $payment): void
    {
        $payment->user->update(['subscribed' => true, 'subscribed_at' => $payment->completed_at]);
    }

    private function fulfillEmployerJobPost(Payment $payment): void
    {
        if (JobPostingPayment::where('id', $payment->id)->exists()) {
            return;
        }

        $input = $payment->payload['job'];
        $relevance = KenyaRelevance::score($input);

        $job = JobListing::create([
            'id' => 'employer--'.Str::uuid(),
            'source_name' => 'Employer',
            'source_id' => (string) $payment->id,
            'source_url' => $input['source_url'],
            'title' => $input['title'],
            'company' => $input['company'],
            'description' => $input['description'],
            'tags' => $input['tags'],
            'location' => $input['location'],
            'remote_type' => $input['remote_type'],
            'salary' => $input['salary'],
            'annual_salary_usd' => null,
            'posted_at' => $payment->completed_at,
            'kenya_friendly' => $relevance['kenyaFriendly'],
            'kenya_score' => $relevance['score'],
            'kenya_reasons' => $relevance['reasons'],
            'origin' => 'employer',
            // Never paywalled to candidates — the employer already paid for
            // reach, so tier is stored for schema consistency only.
            'tier' => 'basic',
            'audience_segments' => Audience::classify($input),
            'posted_by_user_id' => $payment->user_id,
        ]);

        JobPostingPayment::create([
            'id' => $payment->id,
            'user_id' => $payment->user_id,
            'job_listing_id' => $job->id,
            'plan' => $payment->payload['plan'],
            'amount_kes' => $payment->amount_kes,
            'posted_at' => $payment->completed_at,
        ]);
    }
}
