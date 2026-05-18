<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Check if user exists and is locked
        $user = User::where('email', $this->email)->first();
        
        if ($user && method_exists($user, 'isLocked') && $user->isLocked()) {
            $minutes = $user->getRemainingLockTimeMinutes();
            throw ValidationException::withMessages([
                'email' => "Account is locked due to too many failed login attempts. Please try again in {$minutes} minutes.",
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Track failed login attempt
            if ($user && method_exists($user, 'incrementFailedLoginAttempts')) {
                $user->incrementFailedLoginAttempts();
                $remaining = $user->getRemainingAttempts();
                
                if ($remaining > 0) {
                    throw ValidationException::withMessages([
                        'email' => trans('auth.failed') . " You have {$remaining} attempts remaining.",
                    ]);
                } else {
                    throw ValidationException::withMessages([
                        'email' => 'Too many failed attempts. Your account has been locked for 30 minutes and you must change your password.',
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Reset failed attempts on successful login
        if ($user && method_exists($user, 'resetLoginAttempts')) {
            $user->resetLoginAttempts();
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $maxAttempts = (int) setting('max_login_attempts', 5);
        
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
