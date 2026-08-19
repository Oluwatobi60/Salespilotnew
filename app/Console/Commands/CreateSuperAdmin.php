<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superadmin:create {name} {email} {password} {--phone=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new SuperAdmin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $phone = $this->option('phone');

        if (SuperAdmin::where('email', $email)->exists()) {
            $this->error("SuperAdmin with email {$email} already exists.");
            return Command::FAILURE;
        }

        $superadmin = SuperAdmin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone,
        ]);

        $this->info("SuperAdmin {$superadmin->name} ({$superadmin->email}) created successfully.");

        return Command::SUCCESS;
    }
}
