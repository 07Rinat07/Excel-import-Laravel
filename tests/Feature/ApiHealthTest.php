<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_requires_auth(): void
    {
        $this->getJson('/api/health')->assertStatus(401);
    }

    public function test_health_ok_with_auth(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/health')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }
}
