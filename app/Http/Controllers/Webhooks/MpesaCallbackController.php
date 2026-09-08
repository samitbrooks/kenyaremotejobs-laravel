<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Not wired to a live gateway yet — this is the landing spot for Safaricom's
 * Daraja STK push callback once a DarajaGateway exists (see
 * App\Payments\PaymentGateway and config/payments.php). Route is already
 * registered (routes/web.php) and public — Safaricom calls it directly, not
 * through a browser session — so it's ready to point the Daraja app's
 * callback URL at as soon as the gateway itself is built.
 *
 * When that gateway is added, DarajaGateway::initiate() will:
 *   1. Request an OAuth token and send the STK push for $payment->amount_kes
 *      to $payment->phone.
 *   2. Store Safaricom's CheckoutRequestID on $payment->external_reference
 *      and leave $payment->status as 'pending'.
 *
 * This method then needs to:
 *   1. Verify the request is genuinely from Safaricom (IP allowlist and/or
 *      a shared secret in the callback URL — Daraja has no signature
 *      header to check).
 *   2. Look up the Payment by the CheckoutRequestID in the callback body.
 *   3. On ResultCode 0, mark it completed (with the MpesaReceiptNumber as
 *      a second reference) and call PaymentService::fulfill($payment).
 *   4. On any other ResultCode, mark it failed.
 *   5. Always return a 200 with {"ResultCode": 0} — Daraja retries
 *      indefinitely on anything else.
 */
class MpesaCallbackController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments)
    {
        Log::info('[mpesa-callback] received but no gateway is wired up yet', $request->all());

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
