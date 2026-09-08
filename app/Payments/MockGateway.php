<?php

namespace App\Payments;

use App\Models\Payment;

/**
 * Grants immediately without taking real payment — same behavior as the
 * Next.js version's TODO(payments) mocks. Swap the `payments.default`
 * gateway binding in AppServiceProvider for a real one (e.g. a DarajaGateway
 * that sends an M-Pesa STK push and leaves the payment pending until a
 * webhook confirms it) once a real charge needs to happen here; nothing
 * else in the app has to change since callers only depend on the
 * PaymentGateway interface.
 */
class MockGateway implements PaymentGateway
{
    public function initiate(Payment $payment): void
    {
        $payment->update(['status' => 'completed', 'completed_at' => now()]);
    }
}
