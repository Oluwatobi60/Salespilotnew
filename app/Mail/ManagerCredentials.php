<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $manager;

    public $password;

    public $businessName;

    public $addedBy;

    public function __construct(User $manager, string $password, ?string $businessName = null, ?string $addedBy = null)
    {
        $this->manager = $manager;
        $this->password = $password;
        $this->businessName = $businessName;
        $this->addedBy = $addedBy;
    }

    public function build()
    {
        return $this->subject('Your VigCore Manager Account - Login Credentials')
            ->view('emails.manager_credentials')
            ->with([
                'user' => $this->manager,
                'password' => $this->password,
                'businessName' => $this->businessName,
                'managerName' => $this->manager->first_name.' '.$this->manager->surname,
            ]);
    }
}
