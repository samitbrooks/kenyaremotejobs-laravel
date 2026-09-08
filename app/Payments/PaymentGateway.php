<?php

namespace App\Payments;

use App\Models\Payment;

/**
 * The seam a real provider (M-Pesa Daraja, Paystack, ...) plugs into later.
 *
 * initiate() only has to start the charge — it does not have to resolve it.
 * MockGateway resolves synchronously (there's nothing to wait on), but a
 * real gateway's initiate() typically returns immediately after kicking off
 * an async flow (e.g. an STK push to the payer's phone) and leaves the
 * Payment row `pending`; a webhook route then confirms or fails it later.
 * See PaymentService::fulfill() for the part that runs once a payment is
 * actually confirmed, which is shared by both paths.
 */
interface PaymentGateway
{
    public function initiate(Payment $payment): void;
}
