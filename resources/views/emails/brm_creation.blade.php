<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRM Account Created - SalesPilot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .greeting strong {
            color: #667eea;
        }

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .info-box h3 {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #333;
            word-break: break-all;
        }

        .credentials-box {
            background-color: #fffbf0;
            border: 2px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
            text-align: center;
        }

        .credentials-box h3 {
            color: #ff6b6b;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .credential-row {
            margin-bottom: 12px;
        }

        .credential-label {
            color: #666;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .credential-value {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            word-break: break-all;
            border-radius: 4px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .next-steps {
            background-color: #e8f4f8;
            border-left: 4px solid #0288d1;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .next-steps h3 {
            color: #0288d1;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .next-steps ol {
            margin-left: 20px;
            color: #555;
        }

        .next-steps li {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .footer p {
            margin-bottom: 8px;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Welcome to SalesPilot</h1>
            <p>Your BRM Account Has Been Created</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">
                Hello <strong>{{ $brm->name }}</strong>,
            </p>
            <p style="margin-bottom: 20px;">
                Your Business Referral Manager (BRM) account has been successfully created by the SalesPilot administration team. Below are your account details and login credentials.
            </p>

            <!-- BRM Information -->
            <div class="info-box">
                <h3>📋 Account Information</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $brm->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $brm->email }}</span>
                </div>
                @if($brm->phone)
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $brm->phone }}</span>
                </div>
                @endif
                @if($brm->region)
                <div class="info-item">
                    <span class="info-label">Region:</span>
                    <span class="info-value">{{ $brm->region }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Referral Code:</span>
                    <span class="info-value" style="color: #28a745; font-weight: 700;">{{ $brm->referral_code }}</span>
                </div>
            </div>

            <!-- Login Credentials -->
            <div class="credentials-box">
                <h3>⚠️ Important: Save Your Login Credentials</h3>
                <div class="credential-row">
                    <div class="credential-label">Email Address</div>
                    <div class="credential-value">{{ $brm->email }}</div>
                </div>
                <div class="credential-row">
                    <div class="credential-label">Password</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
                <p style="font-size: 12px; color: #ff6b6b; margin-top: 12px;">
                    ⚠️ <strong>For security reasons, you should change this password upon first login.</strong>
                </p>
            </div>

            <!-- Action Button -->
            <div class="button-container">
                <a href="{{ route('login') }}" class="btn">Login to Your Account</a>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3>📌 Next Steps</h3>
                <ol>
                    <li><strong>Log in</strong> to your BRM account using the credentials above</li>
                    <li><strong>Update your password</strong> to something more secure that only you know</li>
                    <li><strong>Share your referral code</strong> ({{ $brm->referral_code }}) with potential customers</li>
                    <li><strong>Complete your profile</strong> with additional business information if needed</li>
                    <li><strong>Monitor customer registrations</strong> that use your referral code</li>
                </ol>
            </div>

            <!-- Support -->
            <p style="margin-top: 30px; color: #666; font-size: 13px;">
                If you have any questions or need assistance, please contact our support team at
                <strong>support@salespilot.com</strong>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; {{ date('Y') }} SalesPilot. All rights reserved.</p>
            <p><a href="{{ route('login') }}">Login to SalesPilot</a></p>
        </div>
    </div>
</body>
</html>
