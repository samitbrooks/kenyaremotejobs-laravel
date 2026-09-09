<?php

namespace App\Payments;

use App\Models\Payment;
use App\Support\KenyanPhone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Safaricom Daraja "Lipa Na M-Pesa Online" (STK Push). initiate() only
 * starts the charge: it fetches an OAuth token, sends the push, and — if
 * Safaricom accepted the request — stores the CheckoutRequestID on
 * $payment->external_reference and leaves status 'pending'. The actual
 * outcome (approved / cancelled / timed out on the buyer's phone) arrives
 * later via Webhooks\MpesaCallbackController, which looks the Payment back
 * up by that same CheckoutRequestID.
 *
 * A failure here (bad phone, Safaricom unreachable, request rejected)
 * throws and marks the payment failed immediately, since there is no
 * callback coming for a push that was never actually sent.
 */
class DarajaGateway implements PaymentGateway
{
    public function initiate(Payment $payment): void
    {
        $phone = $payment->phone ? KenyanPhone::normalize($payment->phone) : null;

        if (! $phone) {
            $payment->update(['status' => 'failed']);

            throw new RuntimeException('Enter a valid M-Pesa phone number (e.g. 07XXXXXXXX).');
        }

        try {
            $token = $this->accessToken();
            $result = $this->stkPush($token, $payment, $phone);
        } catch (RuntimeException $e) {
            Log::warning('[mpesa] STK push failed to initiate', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            $payment->update(['status' => 'failed']);

            throw $e;
        }

        if (($result['ResponseCode'] ?? null) !== '0') {
            $payment->update(['status' => 'failed']);

            throw new RuntimeException($result['ResponseDescription'] ?? $result['errorMessage'] ?? 'Safaricom rejected the request.');
        }

        $payment->update([
            'phone' => $phone,
            'external_reference' => $result['CheckoutRequestID'],
        ]);
    }

    private function baseUrl(): string
    {
        return config('payments.mpesa.environment') === 'live'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    private function accessToken(): string
    {
        $cacheKey = 'mpesa:access-token:'.config('payments.mpesa.environment');

        return Cache::remember($cacheKey, now()->addMinutes(55), function () {
            $response = Http::withBasicAuth(
                config('payments.mpesa.consumer_key'),
                config('payments.mpesa.consumer_secret'),
            )->get($this->baseUrl().'/oauth/v1/generate', ['grant_type' => 'client_credentials']);

            $token = $response->json('access_token');
            if ($response->failed() || ! $token) {
                throw new RuntimeException('Could not authenticate with Safaricom.');
            }

            return $token;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function stkPush(string $token, Payment $payment, string $phone): array
    {
        $shortcode = config('payments.mpesa.shortcode');
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode.config('payments.mpesa.passkey').$timestamp);

        $response = Http::withToken($token)->post($this->baseUrl().'/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $payment->amount_kes,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => config('payments.mpesa.callback_url'),
            'AccountReference' => 'KenyaRemoteJobs',
            'TransactionDesc' => ucfirst(str_replace('_', ' ', $payment->purpose)),
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Safaricom returned an error starting the payment.');
        }

        return $response->json();
    }
}
