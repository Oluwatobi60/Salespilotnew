<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserSubscription;

class SubscriptionRenewed extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;

    public function __construct(User $user, UserSubscription $subscription)
    {
        
        $this->user         = $user;
        $this->subscription = $subscription;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your SalesPilot Subscription Has Been Auto-Renewed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_renewed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
