<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - {{ app_name() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding: 20px;
        }

        .maintenance-container {
            max-width: 600px;
            width: 100%;
        }

        .maintenance-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .maintenance-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .maintenance-message {
            font-size: 1.1rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .maintenance-details {
            background: #f7fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .maintenance-details p {
            margin: 0.5rem 0;
            color: #4a5568;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .maintenance-details i {
            color: #667eea;
            font-size: 1.2rem;
        }

        .refresh-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .refresh-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .footer-text {
            margin-top: 2rem;
            color: white;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 576px) {
            .maintenance-card {
                padding: 2rem 1.5rem;
            }

            .maintenance-title {
                font-size: 1.5rem;
            }

            .maintenance-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-card">
            <div class="maintenance-icon">🔧</div>
            
            <h1 class="maintenance-title">
                We're Currently Under Maintenance
            </h1>
            
            <p class="maintenance-message">
                {{ setting('maintenance_message', 'We are currently performing scheduled maintenance. Please check back soon.') }}
            </p>

            <div class="maintenance-details">
                <p>
                    <i class="bi bi-clock"></i>
                    <span>Estimated downtime: <strong>15-30 minutes</strong></span>
                </p>
                <p>
                    <i class="bi bi-shield-check"></i>
                    <span>Your data is safe and secure</span>
                </p>
                <p>
                    <i class="bi bi-lightning-charge"></i>
                    <span>We're making things faster and better</span>
                </p>
            </div>

            <div class="spinner"></div>

            <button onclick="location.reload()" class="refresh-button">
                <i class="bi bi-arrow-clockwise me-2"></i>Check Status
            </button>
        </div>

        <div class="footer-text text-center">
            <p>Need immediate assistance? Contact us at <strong>{{ support_email() }}</strong></p>
            <p style="font-size: 0.85rem; opacity: 0.8;">© {{ date('Y') }} {{ app_name() }}. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto-refresh every 60 seconds to check if maintenance is over
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
