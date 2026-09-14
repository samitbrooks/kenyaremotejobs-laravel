<?php

namespace App\Services;

use App\Mail\MarketingEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

/**
 * Ported from the Next.js version's src/lib/email.ts. Uses Laravel's own
 * Mail facade (configured via config/mail.php's MAIL_HOST/PORT/USERNAME/
 * PASSWORD/FROM_ADDRESS env vars) rather than a bespoke SMTP client —
 * cPanel hosting typically bundles an email account per domain, so once the
 * domain's live there's an SMTP login ready to drop into those same env
 * vars.
 */
class BulkMailer
{
    /**
     * Checks if email sending can proceed with the current configuration.
     * Supports sendmail (standard cPanel transport), smtp, and log for testing.
     */
    public function isConfigured(): bool
    {
        $mailer = config('mail.default');

        if (in_array($mailer, ['sendmail', 'log', 'array'], true)) {
            return (bool) config('mail.from.address');
        }

        if ($mailer === 'smtp') {
            $host = config('mail.mailers.smtp.host');
            $hasFrom = (bool) config('mail.from.address');
            $hasHost = ! empty($host);
            $isLocal = in_array($host, ['localhost', '127.0.0.1'], true);
            $hasAuth = ! empty(config('mail.mailers.smtp.username')) && ! empty(config('mail.mailers.smtp.password'));

            return $hasFrom && $hasHost && ($isLocal || $hasAuth || app()->environment('local', 'testing'));
        }

        return (bool) config('mail.from.address');
    }

    /**
     * Sends one at a time (not one big BCC) so a bad address doesn't block
     * the rest of the list, and so each recipient only ever sees their own
     * address. Each send goes through App\Mail\MarketingEmail, which carries
     * the List-Unsubscribe headers and a per-user signed unsubscribe link.
     *
     * @param  iterable<User>  $recipients
     * @return array{sent: int, failed: array<int, string>}
     */
    public function sendBulk(iterable $recipients, string $subject, string $message): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                "Email isn't configured yet — verify MAIL_MAILER, MAIL_HOST, and MAIL_FROM_ADDRESS in your .env configuration."
            );
        }

        $sent = 0;
        $failed = [];

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->send(new MarketingEmail($user, $subject, $message));
                $sent++;
            } catch (Throwable $e) {
                $failed[] = "{$user->email}: {$e->getMessage()}";
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Sends a single test email directly to the given address.
     *
     * @return array{success: bool, message: string}
     */
    public function sendTest(string $email, string $subject, string $message): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                "Email isn't configured yet — verify MAIL_MAILER, MAIL_HOST, and MAIL_FROM_ADDRESS in your .env configuration."
            );
        }

        $user = User::where('email', $email)->first() ?? new User([
            'name' => 'Admin Preview',
            'email' => $email,
        ]);

        if (! $user->exists) {
            $user->id = 1;
        }

        try {
            Mail::to($email)->send(new MarketingEmail($user, $subject, $message));

            return [
                'success' => true,
                'message' => "Test email successfully dispatched to {$email}.",
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => "Delivery failed: {$e->getMessage()}",
            ];
        }
    }
}
