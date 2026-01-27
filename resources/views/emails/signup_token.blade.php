<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your SalesPilot Signup Token</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f7f7f7;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .token-box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .token {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            word-break: break-all;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to SalesPilot!</h1>
    </div>

    <div class="content">
        <p>Hello,</p>

        <p>Thank you for your interest in SalesPilot! We're excited to have you join us.</p>

        <p>Your signup verification token has been generated. Please use this token to complete your registration:</p>

        <div class="token-box">
            <p style="margin: 0; font-size: 14px; color: #666;">Your Token:</p>
            <p class="token">{{ $token }}</p>
        </div>

        <p>Click the button below to verify your email and continue with registration:</p>

        <div style="text-align: center;">
            <a href="{{ $tokenUrl }}" class="button">Verify Email & Continue</a>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong> This token will expire in <strong>{{ $expiresIn }} minutes</strong>. Please complete your verification before it expires.
        </div>

        <p>If you didn't request this token, please ignore this email.</p>

        <p>Best regards,<br>The SalesPilot Team</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} SalesPilot. All rights reserved.</p>
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
