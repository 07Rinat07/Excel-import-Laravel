<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateApiTokenCommand extends Command
{
    protected $signature = 'user:token {email} {name=api-token} {--abilities=*} {--expires= : Expiration in days}';

    protected $description = 'Create a personal access token for API integrations.';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->argument('name');
        $abilitiesOption = (string) $this->option('abilities');
        $expiresDays = $this->option('expires');

        $user = User::query()->where('email', $email)->first();
        if (! $user) {
            $this->error('User not found for email: '.$email);

            return Command::FAILURE;
        }

        $abilities = $abilitiesOption === '*' ? ['*'] : array_filter(array_map('trim', explode(',', $abilitiesOption)));
        if (! $abilities) {
            $abilities = ['*'];
        }

        $expiresAt = null;
        if (is_numeric($expiresDays) && (int) $expiresDays > 0) {
            $expiresAt = now()->addDays((int) $expiresDays);
        }

        $token = $user->createToken($name, $abilities, $expiresAt);

        $this->info('Token created:');
        $this->line($token->plainTextToken);

        return Command::SUCCESS;
    }
}
