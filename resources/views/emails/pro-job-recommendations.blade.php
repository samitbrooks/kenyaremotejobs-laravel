<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👑 VIP Pro Real-Time Matches for You, {{ $firstName }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0b0c10; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%;">
    <!-- Hidden preheader text -->
    <div style="display: none; max-height: 0px; overflow: hidden;">
        Real-time priority remote matches + dedicated placement assistance for Pro Member {{ $firstName }}...
    </div>

    <!-- Email Container Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0b0c10; width: 100%; min-height: 100%;">
        <tr>
            <td align="center" style="padding: 32px 12px;">
                <!-- Main Card Box -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #15171e; border: 1px solid #2a2d39; border-radius: 16px; overflow: hidden; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <!-- Top Gold VIP Accent Bar -->
                    <tr>
                        <td style="height: 4px; background: linear-gradient(90deg, #f59e0b, #ff3131, #f59e0b);"></td>
                    </tr>

                    <tr>
                        <td style="padding: 36px 32px 28px 32px;">
                            <!-- Brand & VIP Status Header -->
                            <div style="margin-bottom: 24px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <a href="{{ url('/') }}" style="text-decoration: none; display: inline-flex; align-items: center;">
                                                <span style="font-size: 20px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff;">
                                                    Kenya<span style="color: #ff3131;">RemoteJobs</span>
                                                </span>
                                            </a>
                                        </td>
                                        <td align="right" style="vertical-align: middle;">
                                            <span style="display: inline-block; background-color: rgba(245, 158, 11, 0.15); border: 1px solid #f59e0b; color: #fbbf24; font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; padding: 4px 10px; border-radius: 9999px;">
                                                👑 PRO VIP SUBSCRIBER
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Greeting & Value Affirmation -->
                            <h1 style="margin: 0 0 12px 0; color: #ffffff; font-size: 24px; font-weight: 800; line-height: 1.3; letter-spacing: -0.5px;">
                                Real-time curated matches for you, {{ $firstName }}.
                            </h1>
                            <p style="margin: 0 0 24px 0; color: #94a3b8; font-size: 14px; line-height: 1.6;">
                                As an active <strong style="color: #fbbf24;">Pro Member</strong>, you have 100% unlocked access to every opportunity on our platform with direct recruiter applications, zero paywalls, and priority employer reach. Here are the latest high-relevance remote roles matched to your profile in real time:
                            </p>

                            <!-- List of Curated Pro Roles -->
                            <div style="margin-bottom: 28px;">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; margin-bottom: 12px; border-bottom: 1px solid #232733; padding-bottom: 6px;">
                                    ⚡ Top Suited Opportunities (Real-Time Ingestion)
                                </div>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    @foreach ($jobs as $job)
                                        @php
                                            $kesMonthly = \App\Support\SalaryEstimate::estimateMonthlyKes($job->annual_salary_usd, $job->salary);
                                            $hourly = \App\Support\SalaryEstimate::estimateHourlyUsd($job->annual_salary_usd);
                                        @endphp
                                        <tr>
                                            <td style="padding: 14px 0; border-bottom: 1px solid #232733;">
                                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <a href="{{ url('/jobs/'.$job->id) }}" style="color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; line-height: 1.35; display: block;">
                                                                {{ $job->title }}
                                                            </a>
                                                            <div style="color: #94a3b8; font-size: 12px; margin-top: 4px; line-height: 1.4;">
                                                                <span style="color: #e2e8f0; font-weight: 600;">{{ $job->company }}</span>
                                                                <span style="color: #475569; margin: 0 4px;">&bull;</span>
                                                                <span>{{ $job->remote_type }}</span>
                                                                @if ($kesMonthly)
                                                                    <span style="color: #475569; margin: 0 4px;">&bull;</span>
                                                                    <span style="color: #34d399; font-weight: 700;">{{ $kesMonthly }}</span>
                                                                @elseif ($hourly)
                                                                    <span style="color: #475569; margin: 0 4px;">&bull;</span>
                                                                    <span style="color: #34d399; font-weight: 700;">~${{ $hourly['min'] }}-{{ $hourly['max'] }}/hr</span>
                                                                @endif
                                                                @if ($job->kenya_friendly)
                                                                    <span style="color: #475569; margin: 0 4px;">&bull;</span>
                                                                    <span style="color: #f87171; font-weight: 600;">🇰🇪 Kenya Friendly</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td align="right" style="vertical-align: middle; padding-left: 12px; white-space: nowrap;">
                                                            <a href="{{ url('/jobs/'.$job->id) }}" style="display: inline-block; background-color: #242936; border: 1px solid #3b4254; color: #f1f5f9; text-decoration: none; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 6px;">
                                                                Apply &rarr;
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <!-- View All Active Roles Button -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <a href="{{ $jobsUrl }}" style="display: inline-block; background: linear-gradient(135deg, #ff3131 0%, #b91c1c 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 800; padding: 13px 32px; border-radius: 10px; box-shadow: 0 4px 16px rgba(255, 49, 49, 0.4); text-transform: uppercase; letter-spacing: 0.5px;">
                                    Browse All {{ $totalCount }} Unlocked Roles &rarr;
                                </a>
                            </div>

                            <!-- "WHAT WE CAN DO FOR YOU" (High-Touch Concierge & Client Retention Section) -->
                            <div style="background-color: #1c1f2a; border: 1px solid #373c4e; border-radius: 12px; padding: 22px 24px; margin-bottom: 28px;">
                                <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                    <span style="display: inline-block; background-color: #f59e0b; color: #000000; font-size: 11px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; padding: 3px 8px; border-radius: 4px; margin-right: 8px;">
                                        INCLUDED IN YOUR PRO MEMBERSHIP
                                    </span>
                                </div>
                                <h3 style="margin: 0 0 10px 0; color: #ffffff; font-size: 17px; font-weight: 800; line-height: 1.3;">
                                    What Our VIP Concierge Team Can Do For You:
                                </h3>
                                <p style="margin: 0 0 16px 0; color: #cbd5e1; font-size: 13px; line-height: 1.6;">
                                    We don't just list jobs &mdash; our mission is to ensure you land your next high-paying international remote contract. As a Pro subscriber, our team is directly at your service:
                                </p>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 13px; color: #e2e8f0; line-height: 1.6;">
                                    <tr>
                                        <td style="padding: 6px 0; vertical-align: top; width: 22px; color: #fbbf24; font-weight: bold;">✓</td>
                                        <td style="padding: 6px 0;">
                                            <strong style="color: #ffffff;">1-on-1 CV Optimization:</strong> Send us your resume. We will personally review and optimize its formatting and keywords so it breezes through foreign ATS screeners.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; vertical-align: top; width: 22px; color: #fbbf24; font-weight: bold;">✓</td>
                                        <td style="padding: 6px 0;">
                                            <strong style="color: #ffffff;">Custom Niche Role Scouting:</strong> Tell us your specialty (Software Engineering, Customer Support, Virtual Assistant, Marketing, Sales, etc.). We will actively track unadvertised roles in our partner pipeline.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; vertical-align: top; width: 22px; color: #fbbf24; font-weight: bold;">✓</td>
                                        <td style="padding: 6px 0;">
                                            <strong style="color: #ffffff;">Tailored Application Pitches:</strong> Applying to a competitive role? Reply with the link and we'll help you formulate a personalized cover note directly to the hiring manager.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; vertical-align: top; width: 22px; color: #fbbf24; font-weight: bold;">✓</td>
                                        <td style="padding: 6px 0;">
                                            <strong style="color: #ffffff;">Employer Priority Shortlisting:</strong> When global employers hire through KenyaRemoteJobs, Pro candidates are recommended at the very top of candidate pools.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; vertical-align: top; width: 22px; color: #fbbf24; font-weight: bold;">✓</td>
                                        <td style="padding: 6px 0;">
                                            <strong style="color: #ffffff;">Direct VIP Desk Support:</strong> Have questions regarding salary negotiation, tax for remote USD earnings, or interview prep? You have direct access to our leadership team.
                                        </td>
                                    </tr>
                                </table>

                                <!-- Concierge Call to Action -->
                                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #2f3444;">
                                    <p style="margin: 0 0 10px 0; font-size: 13px; color: #94a3b8;">
                                        <strong>Ready to take advantage of this?</strong> Simply reply directly to this email with your CV or the types of roles you're targeting, and our team will get to work for you right away.
                                    </p>
                                    <a href="mailto:{{ $supportEmail }}?subject=Pro%20Member%20Concierge%20Request%20-%20{{ urlencode($firstName) }}" style="display: inline-block; background-color: #fbbf24; color: #000000; text-decoration: none; font-size: 12px; font-weight: 800; padding: 9px 18px; border-radius: 6px;">
                                        ✉ Reply with Your CV / Target Role &rarr;
                                    </a>
                                </div>
                            </div>

                            <!-- Sign-off -->
                            <div style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                                Dedicated to your remote career success,<br>
                                <strong style="color: #ffffff;">Ivy &amp; The Kenya Remote Jobs VIP Team</strong>
                            </div>

                            <!-- Footer Links & Unsubscribe -->
                            <div style="border-top: 1px solid #232733; padding-top: 20px; text-align: center; font-size: 11px; color: #64748b; line-height: 1.6;">
                                <div style="margin-bottom: 8px;">
                                    <a href="{{ $accountUrl }}" style="color: #94a3b8; text-decoration: underline; margin: 0 8px;">My Pro Account</a>
                                    <span style="color: #334155;">&bull;</span>
                                    <a href="{{ $jobsUrl }}" style="color: #94a3b8; text-decoration: underline; margin: 0 8px;">Real-Time Job Board</a>
                                    <span style="color: #334155;">&bull;</span>
                                    <a href="{{ $resumeBuilderUrl }}" style="color: #94a3b8; text-decoration: underline; margin: 0 8px;">AI Resume Tailor</a>
                                    <span style="color: #334155;">&bull;</span>
                                    <a href="{{ $unsubscribeUrl }}" style="color: #64748b; text-decoration: underline; margin: 0 8px;">Manage Preferences</a>
                                </div>
                                <div>
                                    You received this email because you are a valued <strong>Pro Subscriber</strong> on <a href="{{ url('/') }}" style="color: #64748b; text-decoration: none;">Kenya Remote Jobs</a>.
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
