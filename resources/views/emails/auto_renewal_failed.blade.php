<!DOCTYPE html>
<html>
<head>
    <title>Auto-Renewal Failed</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #e53e3e;">Action Required: Your Auto-Renewal Failed</h2>
        
        <p>Hi {{ $user->business_name ?? 'Valued Customer' }},</p>
        
        <p>We attempted to automatically renew your <strong>{{ ucfirst($subscription->subscriptionPlan->name ?? 'selected') }}</strong> subscription today, but the payment failed.</p>
        
        <p><strong>Reason:</strong> {{ $reason }}</p>
        
        <p>To ensure uninterrupted access to your account and features, please log in and update your payment method or manually renew your subscription as soon as possible.</p>
        
        <div style="margin: 30px 0;">
            <a href="{{ route('plan_pricing') }}" style="background-color: #3182ce; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Renew Subscription Now</a>
        </div>
        
        <p>If you have any questions or need assistance, please reply to this email.</p>
        
        <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
    </div>
</body>
</html>
