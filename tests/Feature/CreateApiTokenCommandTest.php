<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CreateApiTokenCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_token(): void
    {
        $user = User::factory()->create(['email' => 'cmd@example.com']);

        $exitCode = Artisan::call('user:token', [
            'email' => $user->email,
            'name' => 'cmd-token',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertGreaterThan(0, $user->tokens()->count());
    }
}
