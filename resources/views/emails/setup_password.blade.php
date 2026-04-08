<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Password</title>
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
                            🎉 Account Created!
                        </h1>
                        <p style="color:rgba(255,255,255,0.9);margin:10px 0 0;font-size:15px;">
                            One last step — set your password to access SalesPilot
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:40px 30px;">
                        <p style="color:#333;font-size:16px;line-height:1.6;margin:0 0 20px;">
                            Hello <strong>{{ $user->first_name }} {{ $user->surname }}</strong>,
                        </p>
                        <p style="color:#555;font-size:15px;line-height:1.6;margin:0 0 24px;">
                            Your SalesPilot account has been created and your subscription is active. 
                            Click the button below to create your password and gain access to your account.
                        </p>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ route('password.setup', $token) }}"
                                       style="display:inline-block;background:linear-gradient(135deg,#5b21b6,#7c3aed);color:#ffffff;text-decoration:none;padding:15px 36px;border-radius:8px;font-size:16px;font-weight:600;letter-spacing:.3px;">
                                        🔐 Set My Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Expiry notice -->
                        <div style="background-color:#fffbeb;border:1px solid #fbbf24;border-radius:6px;padding:14px 18px;margin-bottom:24px;">
                            <p style="color:#92400e;font-size:13px;margin:0;line-height:1.6;">
                                ⏰ <strong>This link expires in 48 hours.</strong>
                                If it expires, please contact our support team to request a new one.
                            </p>
                        </div>

                        <!-- Fallback URL -->
                        <p style="color:#777;font-size:13px;line-height:1.6;margin:0 0 6px;">
                            If the button doesn't work, copy and paste this link into your browser:
                        </p>
                        <p style="margin:0;word-break:break-all;">
                            <a href="{{ route('password.setup', $token) }}"
                               style="color:#7c3aed;font-size:13px;text-decoration:none;">
                                {{ route('password.setup', $token) }}
                            </a>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f9f9f9;padding:20px 30px;text-align:center;border-top:1px solid #eee;">
                        <p style="color:#999;font-size:12px;margin:0;">
                            © {{ date('Y') }} SalesPilot. All rights reserved.<br>
                            If you didn't create this account, please ignore this email.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
