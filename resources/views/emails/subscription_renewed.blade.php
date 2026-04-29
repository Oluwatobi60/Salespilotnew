<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Auto-Renewed</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f5f5f5;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#5b21b6 0%,#7c3aed 100%);padding:40px 30px;text-align:center;">
                        <h1 style="color:#ffffff;margin:0;font-size:26px;font-weight:600;">
                            ✅ Subscription Auto-Renewed
                        </h1>
                        <p style="color:rgba(255,255,255,0.9);margin:10px 0 0;font-size:15px;">
                            Your {{ app_name() }} subscription has been successfully renewed
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:40px 30px;">
                        <p style="color:#333;font-size:16px;line-height:1.6;margin:0 0 20px;">
                            Hello <strong>{{ $user->first_name }} {{ $user->surname }}</strong>,
                        </p>

                        <div style="background-color:#f0fdf4;border-left:4px solid #16a34a;padding:20px;border-radius:4px;margin-bottom:25px;">
                            <h3 style="color:#15803d;margin:0 0 8px;font-size:17px;">
                                🎉 Great news! Your subscription has been auto-renewed.
                            </h3>
                            <p style="color:#166534;margin:0;font-size:14px;line-height:1.6;">
                                Your access to {{ app_name() }} continues uninterrupted. No action is needed from you.
                            </p>
                        </div>

                        <!-- Renewal Details -->
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background-color:#f9f9f9;border-radius:8px;overflow:hidden;margin-bottom:25px;">
                            <tr>
                                <td style="padding:20px;">
                                    <h3 style="color:#7c3aed;margin:0 0 15px;font-size:17px;border-bottom:2px solid #7c3aed;padding-bottom:10px;">
                                        Renewal Details
                                    </h3>
                                    <table width="100%" cellpadding="8" cellspacing="0">
                                        <tr style="background:#fff;">
                                            <td style="color:#666;font-size:14px;width:40%;border-radius:4px 0 0 4px;padding:10px 12px;">Plan</td>
                                            <td style="color:#333;font-size:14px;font-weight:600;text-transform:capitalize;border-radius:0 4px 4px 0;padding:10px 12px;">
                                                {{ $subscription->subscriptionPlan->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color:#666;font-size:14px;padding:10px 12px;">Business</td>
                                            <td style="color:#333;font-size:14px;font-weight:600;padding:10px 12px;">
                                                {{ $user->business_name }}
                                            </td>
                                        </tr>
                                        <tr style="background:#fff;">
                                            <td style="color:#666;font-size:14px;padding:10px 12px;">Duration</td>
                                            <td style="color:#333;font-size:14px;font-weight:600;padding:10px 12px;">
                                                {{ $subscription->duration_months }} month(s)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color:#666;font-size:14px;padding:10px 12px;">New Start Date</td>
                                            <td style="color:#333;font-size:14px;font-weight:600;padding:10px 12px;">
                                                {{ $subscription->start_date->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        <tr style="background:#fff;">
                                            <td style="color:#666;font-size:14px;padding:10px 12px;">New Expiry Date</td>
                                            <td style="color:#16a34a;font-size:14px;font-weight:700;padding:10px 12px;">
                                                {{ $subscription->end_date->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color:#666;font-size:14px;padding:10px 12px;">Amount</td>
                                            <td style="color:#333;font-size:14px;font-weight:600;padding:10px 12px;">
                                                ₦{{ number_format($subscription->amount_paid, 2) }}
                                            </td>
                                        </tr>
                                        <tr style="background:#fff;">
                                            <td style="color:#666;font-size:14px;padding:10px 12px;">Reference</td>
                                            <td style="color:#666;font-size:13px;font-family:monospace;padding:10px 12px;">
                                                {{ $subscription->payment_reference }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Disable auto-renew note -->
                        <div style="background-color:#fffbeb;border:1px solid #fbbf24;border-radius:6px;padding:16px;margin-bottom:25px;">
                            <p style="color:#92400e;font-size:13px;margin:0;line-height:1.6;">
                                <strong>Want to turn off auto-renewal?</strong><br>
                                Log into your {{ app_name() }} account and visit <em>Settings → Subscription</em> to manage your renewal preferences anytime.
                            </p>
                        </div>

                        <p style="color:#555;font-size:14px;line-height:1.6;margin:0 0 5px;">
                            Thank you for continuing with {{ app_name() }}. If you have any questions, please contact our support team.
                        </p>
                        <p style="color:#555;font-size:14px;line-height:1.6;margin:0;">
                            Best regards,<br>
                            <strong>The {{ app_name() }} Team</strong>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f9f9f9;padding:20px 30px;text-align:center;border-top:1px solid #eee;">
                        <p style="color:#999;font-size:12px;margin:0;">
                            © {{ date('Y') }} {{ app_name() }}. All rights reserved.<br>
                            This is an automated message. Please do not reply directly to this email.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
