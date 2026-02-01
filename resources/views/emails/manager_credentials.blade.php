<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Account Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0d6efd;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 28px;
        }
        .welcome-text {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #0d6efd;
        }
        .credentials-box {
            background-color: #fff;
            border: 2px solid #0d6efd;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .credentials-box h3 {
            color: #0d6efd;
            margin-top: 0;
            font-size: 18px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        .credential-item {
            margin: 15px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .credential-label {
            font-weight: bold;
            color: #555;
            min-width: 120px;
        }
        .credential-value {
            color: #0d6efd;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
        }
        .password-value {
            background-color: #fff3cd;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ffc107;
            color: #856404;
        }
        .login-button {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 25px 0;
            font-size: 16px;
        }
        .login-button:hover {
            background-color: #0b5ed7;
        }
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .security-notice strong {
            color: #856404;
            display: block;
            margin-bottom: 8px;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 6px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Welcome to SalesPilot!</h1>
        </div>

        <div class="welcome-text">
            <h2 style="margin-top: 0; color: #0d6efd;">Hello {{ $managerName }},</h2>
            <p style="margin-bottom: 0;">
                Your manager account has been successfully created for <strong>{{ $businessName }}</strong>.
            </p>
        </div>

        <div class="credentials-box">
            <h3>📋 Your Login Credentials</h3>
            <div class="credential-item">
                <span class="credential-label">Email:</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Password:</span>
                <span class="password-value">{{ $password }}</span>
            </div>
            <div class="credential-item">
                <span class="credential-label">Role:</span>
                <span class="credential-value">Manager</span>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; background-color: #e7f3ff; border-radius: 6px; border-left: 3px solid #0d6efd;">
            <p style="margin: 0; color: #0d6efd; font-weight: bold;">
                ℹ️ Login Information:
            </p>
            <p style="margin: 5px 0 0 0; color: #555;">
                You can login using your <strong>Email</strong> and password.
            </p>
            <li>Use a strong, unique password for your account</li>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="login-button">
                🚀 Login to Your Account
            </a>
        </div>

        <div class="info-section">
            <h4 style="margin-top: 0; color: #0d6efd;">📱 Access Information:</h4>
            <p style="margin: 5px 0;"><strong>Manager Login URL:</strong> <a href="{{ url('/login') }}" style="color: #0d6efd;">{{ url('/login') }}</a></p>
            <p style="margin: 5px 0;"><strong>Status:</strong> Active</p>
        </div>

        <div class="info-section">
            <h4 style="margin-top: 0; color: #0d6efd;">💡 Getting Started:</h4>
            <ol style="margin: 5px 0; padding-left: 20px; line-height: 1.8;">
                <li>Click the "Login to Your Account" button above or visit the manager login page</li>
                <li>Enter your email and the password provided</li>
                <li>Change your password in your profile settings</li>
                <li>Familiarize yourself with the dashboard and available features</li>
                <li>Contact support if you need any assistance</li>
            </ol>
        </div>

        <div class="footer">
            <p><strong>SalesPilot</strong> - Your Comprehensive Inventory Management Solution</p>
            <p style="color: #999; font-size: 12px;">This is an automated email. Please do not reply to this message.</p>
            <p style="color: #999; font-size: 12px;">If you did not expect this email or believe this is an error, please contact your system administrator.</p>
        </div>
    </div>
</body>
</html>



