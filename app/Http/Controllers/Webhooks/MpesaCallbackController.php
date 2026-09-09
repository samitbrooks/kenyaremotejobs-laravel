<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Safaricom posts here once an STK push (started in DarajaGateway::initiate())
 * resolves — approved, cancelled, or timed out on the buyer's phone. Daraja
 * has no signature header to verify a callback actually came from Safaricom,
 * and this site sits behind Cloudflare, so the origin never even sees
 * Safaricom's real source IP for an allowlist check to work against —
 * instead, $secret is a random value baked into the CallBackURL itself
 * (config('payments.mpesa.callback_url')), checked before anything else.
 *
 * Always returns 200 with ResultCode 0 regardless of what's inside — that's
 * what tells Daraja "received, stop retrying"; it isn't a statement about
 * whether the payment succeeded.
 */
class MpesaCallbackController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments, string $secret)
    {
        if (! hash_equals((string) config('payments.mpesa.callback_secret'), $secret)) {
            Log::warning('[mpesa-callback] rejected — bad secret');

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Rejected'], 403);
        }

        $stk = $request->input('Body.stkCallback', []);
        $checkoutRequestId = $stk['CheckoutRequestID'] ?? null;
        $resultCode = $stk['ResultCode'] ?? null;

        Log::info('[mpesa-callback] received', ['checkout_request_id' => $checkoutRequestId, 'result_code' => $resultCode]);

        if (! $checkoutRequestId) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $payment = Payment::where('external_reference', $checkoutRequestId)->first();

        if (! $payment) {
            Log::warning('[mpesa-callback] no matching payment', ['checkout_request_id' => $checkoutRequestId]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // Safaricom can retry a callback delivery — a payment already
        // resolved (by an earlier delivery of this same callback) shouldn't
        // be resolved a second time.
        if (! $payment->isPending()) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ((int) $resultCode === 0) {
            $receipt = collect($stk['CallbackMetadata']['Item'] ?? [])
                ->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;

            $payment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'external_reference' => $receipt ?? $payment->external_reference,
            ]);
            $payments->fulfill($payment);
        } else {
            $payment->update(['status' => 'failed']);
            Log::info('[mpesa-callback] payment failed/cancelled', [
                'payment_id' => $payment->id,
                'result_desc' => $stk['ResultDesc'] ?? null,
            ]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
