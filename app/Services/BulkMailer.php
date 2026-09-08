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
    // Laravel ships a placeholder smtp.host default (127.0.0.1) even when
    // nothing has actually been configured, so host alone isn't a signal —
    // require the mailer to actually be set to smtp, plus real credentials
    // and a from-address, matching the original's all-five-vars check.
    public function isConfigured(): bool
    {
        return config('mail.default') === 'smtp'
            && (bool) config('mail.mailers.smtp.host')
            && (bool) config('mail.mailers.smtp.username')
            && (bool) config('mail.mailers.smtp.password')
            && (bool) config('mail.from.address');
    }

    /**
     * Sends one at a time (not one big BCC) so a bad address doesn't block
     * the rest of the list, and so each recipient only ever sees their own
     * address. Each send goes through App\Mail\MarketingEmail, which carries
     * the List-Unsubscribe headers and a per-user signed unsubscribe link —
     * callers are expected to have already filtered $recipients down to
     * users where receivesMarketingEmail() is true.
     *
     * @param  iterable<User>  $recipients
     * @return array{sent: int, failed: array<int, string>}
     */
    public function sendBulk(iterable $recipients, string $subject, string $message): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                "Email isn't configured yet — set MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, and MAIL_FROM_ADDRESS."
            );
        }

        $sent = 0;
        $failed = [];

        foreach ($recipients as $user) {
            try {
                Mail::to($user)->send(new MarketingEmail($user, $subject, $message));
                $sent++;
            } catch (Throwable) {
                $failed[] = $user->email;
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }
}
