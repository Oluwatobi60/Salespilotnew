# Subscription Upgrade Process

This document explains how the subscription upgrade system works in SalesPilot.

## Overview

The system allows business creators to upgrade their subscription plans at any time. When a user upgrades:
1. Their existing active subscription is automatically cancelled
2. The new subscription is activated immediately
3. The user gets immediate access to the new plan's features

## Database Changes

### New Migration
- **File**: `2026_04_29_000000_add_cancellation_fields_to_user_subscriptions_table.php`
- **Changes**:
  - Added `cancellation_reason` (string, nullable) - Stores why the subscription was cancelled
  - Added `cancelled_at` (timestamp, nullable) - Records when the cancellation occurred

### Updated Model
- **File**: `app/Models/UserSubscription.php`
- **Changes**:
  - Added `cancellation_reason` and `cancelled_at` to `$fillable` array
  - Added `cancelled_at` to `$casts` array as datetime

## Code Changes

### SignupController Updates

#### New Method: `cancelExistingSubscriptions()`
```php
protected function cancelExistingSubscriptions($userId)
```
- Finds all active subscriptions for the user
- Updates status to 'cancelled'
- Sets end_date to current time
- Records cancellation reason as "Upgraded to a new plan"
- Logs the cancellation for audit purposes
- Returns count of cancelled subscriptions

#### Modified Methods

1. **selectPlan()**
   - Removed check that blocked upgrades
   - Now allows users with active subscriptions to select new plans

2. **showPayment()**
   - Removed check that blocked upgrades
   - Users can proceed to payment even with active subscriptions

3. **processPayment()**
   - Calls `cancelExistingSubscriptions()` before creating new subscription
   - Ensures smooth transition from old to new plan

4. **activateFreePlan()**
   - Calls `cancelExistingSubscriptions()` before activating free/trial plan
   - Allows downgrading or switching to free trial

5. **verifyPaystackPayment()**
   - Calls `cancelExistingSubscriptions()` after payment verification
   - Ensures old subscription is cancelled before new one is activated

### View Updates

#### plan_pricing.blade.php
- Shows navigation bar conditionally:
  - Hidden for new users (during signup flow)
  - Hidden for existing users upgrading (shows "Upgrade Your Plan" badge)
- Displays informational message when user has active subscription:
  - Shows current plan name and expiry date
  - Informs user that selecting new plan will cancel current subscription
  - Provides reassurance about immediate activation

## User Flow

### For New Users
1. Register account
2. Verify email
3. Select plan → Menu hidden, shows "Step 2 of 3 — Choose a Plan"
4. Make payment → Menu hidden, shows "Step 2 of 3 — Payment"
5. Set password
6. Access dashboard

### For Existing Users (Upgrade)
1. Click "Upgrade Plan" button in dashboard
2. Redirected to plan_pricing → Menu hidden, shows "Upgrade Your Plan"
3. See current subscription info and upgrade message
4. Select new plan → Old subscription automatically cancelled
5. Make payment → Menu hidden
6. New subscription activated immediately
7. Return to dashboard with new features

## Features

### Automatic Cancellation
- Old subscriptions are cancelled automatically
- No manual intervention required
- Immediate activation of new plan
- No overlap or conflicts between plans

### Audit Trail
- All cancellations are logged
- Cancellation reason stored in database
- Timestamp of cancellation recorded
- Can track upgrade history for each user

### User Experience
- Seamless upgrade process
- Clear communication about what happens
- No waiting period
- Immediate access to new features
- No need to wait for old subscription to expire

## Database Schema

### user_subscriptions Table
```sql
- cancellation_reason: VARCHAR(255) NULL
- cancelled_at: TIMESTAMP NULL
```

## Testing

### Test Scenarios
1. **New user signup** → Should create first subscription
2. **Upgrade from free to paid** → Should cancel free, activate paid
3. **Upgrade from basic to premium** → Should cancel basic, activate premium
4. **Downgrade from premium to basic** → Should cancel premium, activate basic
5. **Switch from one paid plan to another** → Should cancel old, activate new

### Expected Results
- Only one active subscription per user at any time
- Old subscription marked as 'cancelled'
- New subscription status is 'active'
- Cancellation reason is recorded
- User has immediate access to new plan features

## Logging

All subscription cancellations are logged with:
- User ID
- Subscription ID
- Old plan name
- Cancellation timestamp

View logs in: `storage/logs/laravel.log`

## Error Handling

- If cancellation fails, new subscription won't be created
- Transaction safety ensures data consistency
- Error messages displayed to user if issues occur
- Support team can review logs for troubleshooting

## Future Enhancements

Potential improvements:
1. Prorated refunds for cancelled subscriptions
2. Email notifications for subscription changes
3. Subscription history view for users
4. Comparison of old vs new plan features
5. Confirmation modal before upgrade
6. Undo upgrade within X hours
7. BRM commission handling for upgrades

## Running the Migration

To apply the database changes:

```bash
php artisan migrate
```

This will add the cancellation tracking fields to the user_subscriptions table.

## Notes

- Cancelled subscriptions are kept in the database for historical records
- Users can view their subscription history (if implemented)
- The system prevents multiple active subscriptions per user
- Free trial subscriptions can also be upgraded/cancelled
