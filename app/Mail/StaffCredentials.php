<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Staffs;

class StaffCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $password;
    public $businessName;
    public $managerName;

    /**
     * Create a new message instance.
     */
    public function __construct(Staffs $staff, string $password, ?string $businessName = null, ?string $managerName = null)
    {
        $this->staff = $staff;
        $this->password = $password;
        $this->businessName = $businessName;
        $this->managerName = $managerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your SalesPilot Staff Account - Login Credentials',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.staff_credentials',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
