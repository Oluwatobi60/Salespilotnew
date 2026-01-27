# Laravel Scheduler Setup for Subscription Expiry

The system now automatically checks and expires subscriptions. To enable this, you need to set up the Laravel scheduler.

## What Was Implemented

1. **Command**: `subscriptions:check-expired` - Checks and updates expired subscriptions
2. **Schedule**: Runs daily at midnight
3. **Middleware**: `CheckSubscriptionStatus` - Real-time check on every request

## Setting Up the Scheduler

### For Windows (Development)

1. Open Task Scheduler
2. Create a new task with these settings:
   - **Trigger**: Daily at midnight (or every minute for testing)
   - **Action**: Start a program
   - **Program**: `php`
   - **Arguments**: `C:\Users\TOBESTIC\laravel-projects\salespilot\artisan schedule:run`
   - **Start in**: `C:\Users\TOBESTIC\laravel-projects\salespilot`

### For Linux/Production Server

Add this cron entry:
```bash
* * * * * cd /path/to/salespilot && php artisan schedule:run >> /dev/null 2>&1
```

## Manual Testing

You can manually run the command anytime:
```bash
php artisan subscriptions:check-expired
```

## How It Works

1. **Scheduled Command**: Runs daily to check all active subscriptions and marks expired ones
2. **Middleware**: Checks subscription status on every authenticated request and automatically logs out users with expired subscriptions
3. **Login Verification**: Prevents login if subscription is expired

## What Happens When Subscription Expires

- User is logged out automatically
- Status changes from 'active' to 'expired' in database
- User sees error message: "Your subscription has expired. Please renew to continue using SalesPilot."
- User is redirected to login page with option to view plans
