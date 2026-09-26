<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $firstName }}, we found remote and/or flexible jobs you might like!</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f1015; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%;">
    <!-- Preheader text preview -->
    <div style="display: none; max-height: 0px; overflow: hidden; font-size: 1px; line-height: 1px; color: #0f1015; opacity: 0;">
        Activate your 24-hour free trial pass to browse and apply to verified remote jobs open to Kenya...
    </div>

    <!-- Outer Container Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0f1015; width: 100%; min-height: 100%;">
        <tr>
            <td align="center" style="padding: 24px 12px 40px 12px;">
                <!-- Main Email Card (FlexJobs Style) -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 580px; background-color: #191b21; border: 1px solid #2d3039; border-radius: 14px; overflow: hidden; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    
                    <!-- Header Section: Brand & Tagline -->
                    <tr>
                        <td style="padding: 28px 30px 20px 30px; border-bottom: 2px solid #282a33; background-color: #16181d;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <a href="{{ url('/') }}" style="text-decoration: none; display: inline-block;">
                                            <span style="font-size: 22px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff;">
                                                Kenya<span style="color: #ff4820;">RemoteJobs</span>
                                            </span>
                                        </a>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; color: #38bdf8;">
                                            FIND A BETTER WAY TO WORK
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Body Content -->
                    <tr>
                        <td style="padding: 32px 30px 24px 30px;">
                            <!-- Salutation -->
                            <p style="margin: 0 0 16px 0; color: #f1f5f9; font-size: 16px; font-weight: 700; line-height: 1.5;">
                                Dear {{ $firstName }},
                            </p>

                            <!-- Intro Message -->
                            <p style="margin: 0 0 16px 0; color: #cbd5e1; font-size: 14px; line-height: 1.6;">
                                Thank you for checking out KenyaRemoteJobs! We hope you&rsquo;ll decide to join to access our hand-screened and vetted remote and flexible jobs.
                            </p>

                            <p style="margin: 0 0 24px 0; color: #cbd5e1; font-size: 14px; line-height: 1.6;">
                                To help you take your next career step, we have unlocked a <strong style="color: #ffffff;">complimentary 24-hour full access trial pass</strong> for your account. You can browse, search, and apply directly to every single role with zero payment required.
                            </p>

                            <!-- Section Header -->
                            <div style="margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 1px solid #2d3039;">
                                <p style="margin: 0; color: #ffffff; font-size: 14px; font-weight: 800; letter-spacing: -0.2px;">
                                    Here are some remote and flexible jobs for you!
                                </p>
                            </div>

                            <!-- Curated Sample Jobs -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 28px;">
                                @forelse ($jobs as $job)
                                    @php
                                        $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
                                        $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
                                    @endphp
                                    <tr>
                                        <td style="padding: 14px 0; border-bottom: 1px solid #242730;">
                                            <a href="{{ $trialUrl }}" style="color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; line-height: 1.4; display: block;">
                                                {{ $job->title }}
                                            </a>
                                            <div style="color: #94a3b8; font-size: 12px; margin-top: 5px; line-height: 1.4;">
                                                <span style="color: #e2e8f0; font-weight: 600;">{{ $job->company }}</span>
                                                <span style="color: #475569; margin: 0 5px;">&bull;</span>
                                                <span>{{ $job->remote_type }}</span>
                                                @if ($kesMonthly)
                                                    <span style="color: #475569; margin: 0 5px;">&bull;</span>
                                                    <span style="color: #34d399; font-weight: 700;">{{ $kesMonthly }}</span>
                                                @elseif ($hourly)
                                                    <span style="color: #475569; margin: 0 5px;">&bull;</span>
                                                    <span style="color: #34d399; font-weight: 700;">~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr</span>
                                                @endif
                                                @if ($job->kenya_friendly)
                                                    <span style="color: #475569; margin: 0 5px;">&bull;</span>
                                                    <span style="color: #f87171; font-weight: 700;">🇰🇪 Kenya-Friendly</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td style="padding: 12px 0; color: #94a3b8; font-size: 13px;">
                                            Fresh verified remote roles are updated every 6 hours.
                                        </td>
                                    </tr>
                                @endforelse
                            </table>

                            <!-- Signoff Message -->
                            <p style="margin: 0 0 24px 0; color: #cbd5e1; font-size: 14px; line-height: 1.6;">
                                Thank you!<br>
                                <strong style="color: #ffffff;">The KenyaRemoteJobs Team</strong>
                            </p>

                            <!-- Big Primary CTA Button (FlexJobs Orange/Red) -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 28px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $trialUrl }}" style="display: block; width: 100%; max-width: 480px; box-sizing: border-box; background: linear-gradient(135deg, #ff4820 0%, #e03212 100%); background-color: #ff4820; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 800; padding: 16px 28px; border-radius: 10px; text-align: center; letter-spacing: 0.2px; box-shadow: 0 6px 18px rgba(255, 72, 32, 0.4);">
                                            Claim Your 24-Hour Free Trial! &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Note below button -->
                            <p style="margin: 0; text-align: center; color: #64748b; font-size: 11px;">
                                One click to activate &bull; No credit card or payment required &bull; Direct application access
                            </p>
                        </td>
                    </tr>

                    <!-- Social Proof / Trust Section (FlexJobs Blue/Teal Band) -->
                    <tr>
                        <td style="background-color: #0d2836; border-top: 1px solid #144055; padding: 20px 30px; text-align: center;">
                            <p style="margin: 0 0 6px 0; color: #38bdf8; font-size: 12px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">
                                Our Members Have Been Hired by Top Global Teams
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 12px; font-weight: 500; line-height: 1.5;">
                                Automattic &middot; Deel &middot; Turing &middot; GitLab &middot; Remote &middot; Wise &middot; Omnipresent
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td style="padding: 24px 30px; background-color: #121318; border-top: 1px solid #23252d; text-align: center;">
                            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 11px; line-height: 1.5;">
                                You are receiving this because you registered an account on {{ config('site.name') }}.
                            </p>
                            <p style="margin: 0; color: #475569; font-size: 11px;">
                                <a href="{{ $unsubscribeUrl }}" style="color: #94a3b8; text-decoration: underline;">
                                    Unsubscribe
                                </a> from future marketing announcements at any time.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
