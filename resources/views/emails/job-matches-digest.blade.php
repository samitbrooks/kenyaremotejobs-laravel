<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $firstName }}, {{ $totalCount }} open roles for you</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0c0d10; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%;">
    <!-- Hidden preheader text -->
    <div style="display: none; max-height: 0px; overflow: hidden;">
        Discover {{ $totalCount }} new international remote roles open to Kenya &amp; East Africa Time (UTC+3)...
    </div>

    <!-- Email Container Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0c0d10; width: 100%; min-height: 100%;">
        <tr>
            <td align="center" style="padding: 30px 12px;">
                <!-- Main Card Box (Matches Remote4Africa Aesthetic) -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 580px; background-color: #17181c; border: 1px solid #27282e; border-radius: 14px; overflow: hidden; text-align: left;">
                    <tr>
                        <td style="padding: 36px 30px 24px 30px;">
                            <!-- Brand Header -->
                            <div style="margin-bottom: 24px;">
                                <a href="{{ url('/') }}" style="text-decoration: none; display: inline-flex; align-items: center; color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">
                                    <span style="color: #ffffff;">Kenya</span><span style="color: #ff3131; margin-left: 2px;">RemoteJobs</span>
                                </a>
                            </div>

                            <!-- Job Matches Pill Badge -->
                            <div style="margin-bottom: 12px;">
                                <span style="display: inline-block; background-color: #23252b; border: 1px solid #2f323a; color: #a1a1aa; font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: 4px 10px; border-radius: 9999px;">
                                    JOB MATCHES
                                </span>
                            </div>

                            <!-- Headline -->
                            <h1 style="margin: 0 0 24px 0; color: #ffffff; font-size: 24px; font-weight: 800; line-height: 1.25; letter-spacing: -0.5px;">
                                New roles for you, {{ $firstName }}.
                            </h1>

                            <!-- List of Roles -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                @foreach ($jobs as $job)
                                    @php
                                        $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
                                        $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
                                    @endphp
                                    <tr>
                                        <td style="padding: 14px 0; border-bottom: 1px solid #25262c;">
                                            <a href="{{ url('/jobs/'.$job->id) }}" style="color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; line-height: 1.4; display: block;">
                                                {{ $job->title }}
                                            </a>
                                            <div style="color: #94a3b8; font-size: 12px; margin-top: 5px; line-height: 1.4;">
                                                <span style="color: #cbd5e1; font-weight: 500;">{{ $job->company }}</span>
                                                <span style="color: #475569; margin: 0 4px;">•</span>
                                                <span>{{ $job->remote_type }}</span>
                                                @if ($kesMonthly)
                                                    <span style="color: #475569; margin: 0 4px;">•</span>
                                                    <span style="color: #34d399; font-weight: 600;">{{ $kesMonthly }}</span>
                                                @elseif ($hourly)
                                                    <span style="color: #475569; margin: 0 4px;">•</span>
                                                    <span style="color: #34d399; font-weight: 600;">~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr</span>
                                                @endif
                                                @if ($job->kenya_friendly)
                                                    <span style="color: #475569; margin: 0 4px;">•</span>
                                                    <span style="color: #ff3131; font-weight: 600;">🇰🇪 Kenya-Friendly</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <!-- View All CTA Button -->
                            <div style="padding-top: 28px; padding-bottom: 24px;">
                                <a href="{{ $jobsUrl }}" style="display: inline-block; background-color: #ff3131; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 30px; border-radius: 8px; text-align: center; box-shadow: 0 4px 14px rgba(255, 49, 49, 0.3);">
                                    View All {{ $totalCount }} Roles &rarr;
                                </a>
                            </div>

                            <!-- CV Evaluation Callout Card (Matching Screenshot) -->
                            <div style="background-color: #1e2026; border: 1px solid #2a2d36; border-radius: 10px; padding: 18px 20px; margin-top: 8px; margin-bottom: 28px;">
                                <div style="color: #e2e8f0; font-size: 13px; line-height: 1.5;">
                                    Does your CV meet remote-work hiring standards? Get detailed insights &mdash; formatting, structure, keywords.
                                </div>
                                <div style="margin-top: 8px;">
                                    <a href="{{ $resumeBuilderUrl }}" style="color: #38bdf8; text-decoration: underline; font-size: 13px; font-weight: 700;">
                                        Evaluate your CV &rarr;
                                    </a>
                                </div>
                            </div>

                            <!-- Sign-off -->
                            <div style="color: #94a3b8; font-size: 13px; line-height: 1.5; margin-bottom: 16px;">
                                The Kenya Remote Jobs Team
                            </div>

                            <!-- Footer Links & Unsubscribe -->
                            <div style="border-top: 1px solid #25262c; padding-top: 20px; margin-top: 20px; text-align: center; font-size: 11px; color: #64748b; line-height: 1.6;">
                                <div style="margin-bottom: 8px;">
                                    <a href="{{ $unsubscribeUrl }}" style="color: #94a3b8; text-decoration: underline; margin: 0 8px;">Unsubscribe</a>
                                    <span style="color: #334155;">•</span>
                                    <a href="{{ $jobsUrl }}" style="color: #94a3b8; text-decoration: underline; margin: 0 8px;">Job Alerts</a>
                                    <span style="color: #334155;">•</span>
                                    <a href="{{ url('/about') }}" style="color: #94a3b8; text-decoration: underline; margin: 0 8px;">About Us</a>
                                </div>
                                <div>
                                    You received this because you created an account on <a href="{{ url('/') }}" style="color: #64748b; text-decoration: none;">Kenya Remote Jobs</a>.
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
