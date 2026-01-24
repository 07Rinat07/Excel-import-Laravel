<?php

namespace Tests\Integration;

use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TypeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_types_endpoint_lists_types(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Type::create(['title' => 'Type A']);
        Type::create(['title' => 'Type B']);

        $response = $this->getJson('/api/types');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                ['id', 'title'],
            ],
        ]);
    }
}
