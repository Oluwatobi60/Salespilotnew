# Email Configuration Guide for SalesPilot

## Current Setup
The application is configured to use SMTP for sending emails. You need to provide your email credentials.

## Gmail Configuration (Recommended for Testing)

### Step 1: Enable 2-Factor Authentication
1. Go to your Google Account: https://myaccount.google.com/
2. Navigate to **Security**
3. Enable **2-Step Verification**

### Step 2: Generate App Password
1. Go to: https://myaccount.google.com/apppasswords
2. Select **Mail** as the app
3. Select **Other (Custom name)** as the device
4. Enter "SalesPilot" as the name
5. Click **Generate**
6. Copy the 16-character password (no spaces)

### Step 3: Update .env File
Open `.env` file and update these lines:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com          # Your Gmail address
MAIL_PASSWORD=xxxx xxxx xxxx xxxx            # The 16-char app password from Step 2
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@salespilot.com"   # Can be any email
MAIL_FROM_NAME="SalesPilot"
```

### Step 4: Clear Config Cache
Run this command in your terminal:
```bash
php artisan config:clear
```

---

## Alternative: Mailtrap (Best for Development/Testing)

Mailtrap catches all emails in a fake inbox - perfect for testing!

### Setup Mailtrap:
1. Sign up at: https://mailtrap.io/
2. Go to **Email Testing** → **Inboxes**
3. Select your inbox
4. Click **Show Credentials**
5. Copy the credentials

### Update .env:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@salespilot.com"
MAIL_FROM_NAME="SalesPilot"
```

---

## Alternative: SendGrid (Production Ready)

### Setup SendGrid:
1. Sign up at: https://sendgrid.com/
2. Create an API Key
3. Update .env:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@salespilot.com"
MAIL_FROM_NAME="SalesPilot"
```

---

## Testing Email Configuration

After configuration, test by running:

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from SalesPilot', function($message) {
    $message->to('your-test-email@gmail.com')
            ->subject('Test Email');
});
```

---

## Troubleshooting

### "Failed to authenticate" Error
- Double-check your email and password
- Make sure you're using an App Password (not your regular Gmail password)
- Verify 2FA is enabled for Gmail

### "Connection timeout" Error
- Check if port 587 is blocked by your firewall
- Try port 465 with MAIL_ENCRYPTION=ssl

### Emails not sending
- Clear config cache: `php artisan config:clear`
- Check `storage/logs/laravel.log` for errors
- Make sure queue is running if using queues: `php artisan queue:work`

---

## Production Recommendations

For production, consider:
1. **SendGrid** - Reliable, scalable, good free tier
2. **Amazon SES** - Cheap, reliable for high volume
3. **Mailgun** - Developer-friendly API
4. **Postmark** - Fast delivery, great for transactional emails

**Important:** Never commit your actual credentials to Git!
Use environment variables and keep `.env` secure.
