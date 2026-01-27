<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Activated</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">
                                🎉 Welcome to SalesPilot!
                            </h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">
                                Your subscription has been successfully activated
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Hello <strong>{{ $user->first_name }} {{ $user->surname }}</strong>,
                            </p>

                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                                Great news! Your SalesPilot subscription has been successfully activated. You now have full access to all features of your selected plan.
                            </p>

                            <!-- Subscription Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9f9f9; border-radius: 8px; overflow: hidden; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #4CAF50; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
                                            Subscription Details
                                        </h3>

                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="color: #666; font-size: 14px; width: 40%;">Plan:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600; text-transform: capitalize;">
                                                    {{ $subscription->subscriptionPlan->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Duration:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">
                                                    {{ $subscription->duration_months == 1 ? '1 Month' : ($subscription->duration_months == 12 ? '1 Year' : $subscription->duration_months . ' Months') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Start Date:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">
                                                    {{ $subscription->start_date->format('F d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Expiry Date:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">
                                                    {{ $subscription->end_date->format('F d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Amount Paid:</td>
                                                <td style="color: #4CAF50; font-size: 16px; font-weight: 700;">
                                                    ₦{{ number_format($subscription->amount_paid, 2) }}
                                                </td>
                                            </tr>
                                            @if($subscription->payment_reference)
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Reference:</td>
                                                <td style="color: #333; font-size: 14px; font-family: monospace;">
                                                    {{ $subscription->payment_reference }}
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Business Information -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f8f0; border-radius: 8px; overflow: hidden; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #4CAF50; margin: 0 0 15px 0; font-size: 18px;">
                                            Your Business Details
                                        </h3>

                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="color: #666; font-size: 14px; width: 40%;">Business Name:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">{{ $user->business_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Branch:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">{{ $user->branch_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666; font-size: 14px;">Location:</td>
                                                <td style="color: #333; font-size: 14px; font-weight: 600;">{{ $user->state }}, {{ $user->local_govt }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <div style="background-color: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                                <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 16px;">
                                    🚀 Getting Started
                                </h3>
                                <p style="color: #856404; margin: 0; font-size: 14px; line-height: 1.6;">
                                    Log in to your dashboard to start managing your inventory, tracking sales, and growing your business with SalesPilot!
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('manager') }}" style="display: inline-block; background-color: #4CAF50; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);">
                                            Go to Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0;">
                                If you have any questions or need assistance, feel free to contact our support team at
                                <a href="mailto:support@salespilot.com" style="color: #4CAF50; text-decoration: none;">support@salespilot.com</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f5f5f5; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="color: #999; font-size: 12px; margin: 0 0 5px 0;">
                                This is an automated email. Please do not reply to this message.
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
