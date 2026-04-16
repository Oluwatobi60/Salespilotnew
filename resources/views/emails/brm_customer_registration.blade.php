<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }
        .greeting strong {
            color: #667eea;
        }
        .customer-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .info-label {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 4px;
        }
        .info-value {
            color: #333;
        }
        .highlight {
            background-color: #fef3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: 500;
            color: #856404;
            text-align: center;
        }
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px;
        }
        .cta-button:hover {
            opacity: 0.9;
            text-decoration: none;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
        }
        .badge {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 New Customer Registration</h1>
            <p>A new customer has registered using your referral code</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $brm->name }}</strong>,
            </div>

            <p>
                Great news! A new customer has just registered on SalesPilot using your referral code.
                Below are their details:
            </p>

            <!-- Customer Information -->
            <div class="customer-info">
                <h3 style="margin-top: 0; color: #667eea;">Customer Information</h3>

                <div class="info-row">
                    <div style="grid-column: 1 / -1;">
                        <div class="info-label">Business Name</div>
                        <div class="info-value" style="font-size: 16px; font-weight: 600;">{{ $customer->business_name }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div>
                        <div class="info-label">Contact Person</div>
                        <div class="info-value">{{ $customer->first_name }} {{ $customer->surname }}</div>
                    </div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <a href="mailto:{{ $customer->email }}" style="color: #667eea; text-decoration: none;">
                                {{ $customer->email }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div>
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $customer->phone_number }}</div>
                    </div>
                    <div>
                        <div class="info-label">Location</div>
                        <div class="info-value">{{ $customer->state }}, {{ $customer->local_govt }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div style="grid-column: 1 / -1;">
                        <div class="info-label">Business Address</div>
                        <div class="info-value">{{ $customer->address }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div>
                        <div class="info-label">Branch Name</div>
                        <div class="info-value">{{ $customer->branch_name }}</div>
                    </div>
                    <div>
                        <div class="info-label">Registration Date</div>
                        <div class="info-value">{{ $customer->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Highlight Box -->
            <div class="highlight">
                ✨ This customer found you through your referral code: <strong>{{ $customer->referral_code }}</strong>
            </div>

            <!-- Next Steps -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #0066cc; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h3 style="margin-top: 0; color: #0066cc;">Next Steps</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>The customer will be setting up their plan shortly</li>
                    <li>Once they complete their subscription, they will be fully set up</li>
                    <li>You can follow up with them to provide support and ensure successful onboarding</li>
                </ul>
            </div>

            <p style="text-align: center; color: #666; font-size: 14px;">
                This is an automatic notification. Please keep the customer's details for your records.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>SalesPilot</strong> - Business Relation Manager Portal
            </p>
            <p>
                © {{ date('Y') }} SalesPilot. All rights reserved.
            </p>
            <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                This email was sent to you as a Business Relation Manager because a new customer
                registered using your referral code.
            </p>
        </div>
    </div>
</body>
</html>
