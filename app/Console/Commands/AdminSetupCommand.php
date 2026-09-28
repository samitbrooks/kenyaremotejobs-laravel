<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('admin:setup {--email= : The email address for the administrator} {--password= : The secure password to assign}')]
#[Description('Create or update administrator credentials and ensure admin allowlist access')]
class AdminSetupCommand extends Command
{
    public function handle(): int
    {
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Administrator Email', 'admin@example.com'))));
        $password = (string) ($this->option('password') ?: $this->secret('Administrator Password'));

        if (empty($password)) {
            $password = Str::random(16);
            $this->warn("No password provided. Generated secure password: {$password}");
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info("✓ Created administrator user [{$email}].");
        } else {
            $user->password = Hash::make($password);
            $user->save();
            $this->info("✓ Updated credentials for administrator user [{$email}].");
        }

        $adminEmails = config('jobs.admin_emails', []);
        if (! in_array($email, $adminEmails, true)) {
            $this->warn("Notice: {$email} is not in ADMIN_EMAILS in your .env file.");
            $this->line("Add it to your .env: ADMIN_EMAILS={$email}");
        } else {
            $this->info("✓ Email [{$email}] is verified in ADMIN_EMAILS allowlist.");
        }

        $this->info('✓ Administrator can now log in at /admin/login');

        return self::SUCCESS;
    }
}
