<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'secret123',
            'is_admin' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'new-user@example.com',
            'is_admin' => true,
        ]);

        $user = User::where('email', 'new-user@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'email' => 'blocked-user@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'blocked-user@example.com',
        ]);
    }
}
