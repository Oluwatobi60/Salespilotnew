<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expiry Reminder</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">
                                ⏰ Subscription Expiry Reminder
                            </h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">
                                Your subscription is expiring soon!
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Hello <strong>{{ $user->first_name }} {{ $user->surname }}</strong>,
                            </p>

                            @if($daysRemaining > 0)
                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 4px; margin-bottom: 25px;">
                                <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 18px;">
                                    ⚠️ Your subscription will expire in {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }}!
                                </h3>
                                <p style="color: #856404; margin: 0; font-size: 14px; line-height: 1.6;">
                                    Don't lose access to your business management tools. Renew your subscription today to continue enjoying uninterrupted service.
                                </p>
                            </div>
                            @else
                            <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 20px; border-radius: 4px; margin-bottom: 25px;">
                                <h3 style="color: #721c24; margin: 0 0 10px 0; font-size: 18px;">
                                    🚨 Your subscription expires today!
                                </h3>
                                <p style="color: #721c24; margin: 0; font-size: 14px; line-height: 1.6;">
                                    Renew now to avoid losing access to your SalesPilot account and business data.
                                </p>
                            </div>
                            @endif

                            <!-- Subscription Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9; border-radius: 8px; overflow: hidden; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #ffc107; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #ffc107; padding-bottom: 10px;">
                                            Current Subscription Details
                                        </h3>

                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="color: #666; font-size: 14px; width: 40%;">Plan:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600; text-transform: capitalize;">
                                                    {{ $subscription->subscriptionPlan->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Business Name:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">
                                                    {{ $user->business_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Expiry Date:</td>
                                                <td style="color: #dc3545; font-size: 16px; font-weight: 700;">
                                                    {{ $subscription->end_date->format('F d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Days Remaining:</td>
                                                <td style="color: {{ $daysRemaining > 2 ? '#ffc107' : '#dc3545' }}; font-size: 16px; font-weight: 700;">
                                                    {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Benefits Section -->
                            <div style="background-color: #e8f5e9; border-left: 4px solid #4CAF50; padding: 20px; border-radius: 4px; margin-bottom: 25px;">
                                <h3 style="color: #2e7d32; margin: 0 0 10px 0; font-size: 16px;">
                                    ✨ What You'll Continue to Enjoy:
                                </h3>
                                <ul style="color: #2e7d32; margin: 10px 0 0 0; padding-left: 20px; line-height: 1.8;">
                                    <li>Uninterrupted access to inventory management</li>
                                    <li>Real-time sales tracking and reporting</li>
                                    <li>Seamless staff and customer management</li>
                                    <li>Priority customer support</li>
                                </ul>
                            </div>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('plan_pricing') }}" style="display: inline-block; background-color: #4CAF50; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);">
                                            Renew Subscription Now
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0; text-align: center;">
                                Need help? Contact our support team at
                                <a href="mailto:support@salespilot.com" style="color: #4CAF50; text-decoration: none;">support@salespilot.com</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f5f5f5; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="color: #999; font-size: 12px; margin: 0 0 5px 0;">
                                This is an automated reminder. You will receive daily reminders until your subscription is renewed.
                            </p>
                            <p style="color: #999; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} SalesPilot. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
