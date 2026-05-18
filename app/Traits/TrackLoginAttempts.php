<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait TrackLoginAttempts
{
    /**
     * Check if the user account is locked due to too many failed attempts.
     */
    public function isLocked(): bool
    {
        if ($this->locked_until && Carbon::parse($this->locked_until)->isFuture()) {
            return true;
        }

        // If lock time has passed, reset the lock
        if ($this->locked_until && Carbon::parse($this->locked_until)->isPast()) {
            $this->resetLoginAttempts();
        }

        return false;
    }

    /**
     * Get the remaining lock time in minutes.
     */
    public function getRemainingLockTimeMinutes(): int
    {
        if (!$this->locked_until) {
            return 0;
        }

        $remaining = Carbon::parse($this->locked_until)->diffInMinutes(Carbon::now(), false);
        return max(0, (int) ceil($remaining));
    }

    /**
     * Increment failed login attempts.
     */
    public function incrementFailedLoginAttempts(): void
    {
        $maxAttempts = (int) setting('max_login_attempts', 5);
        
        $this->failed_login_attempts = ($this->failed_login_attempts ?? 0) + 1;
        $this->last_failed_login_at = Carbon::now();

        // Lock account if max attempts reached
        if ($this->failed_login_attempts >= $maxAttempts) {
            $this->locked_until = Carbon::now()->addMinutes(30); // Lock for 30 minutes
        }

        $this->save();
    }

    /**
     * Reset login attempts (on successful login or after lock expires).
     */
    public function resetLoginAttempts(): void
    {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->last_failed_login_at = null;
        $this->save();
    }

    /**
     * Get the maximum allowed login attempts from settings.
     */
    public function getMaxLoginAttempts(): int
    {
        return (int) setting('max_login_attempts', 5);
    }

    /**
     * Get remaining attempts before lockout.
     */
    public function getRemainingAttempts(): int
    {
        $max = $this->getMaxLoginAttempts();
        $remaining = $max - ($this->failed_login_attempts ?? 0);
        return max(0, $remaining);
    }
}
