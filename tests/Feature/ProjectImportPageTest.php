<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectImportPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_project_import_page(): void
    {
        $response = $this->get(route('project.import'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_project_import_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('project.import'));

        $response->assertOk();
    }
}
