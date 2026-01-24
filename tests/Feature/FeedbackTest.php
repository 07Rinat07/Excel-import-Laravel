<?php

namespace Tests\Feature;

use App\Models\FeedbackMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_page_can_be_rendered(): void
    {
        $response = $this->get(route('feedback.create'));

        $response->assertOk();
    }

    public function test_feedback_message_is_stored(): void
    {
        $response = $this->post(route('feedback.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello from feedback.',
        ]);

        $response->assertRedirect(route('feedback.create'));
        $this->assertDatabaseHas('feedback_messages', [
            'email' => 'test@example.com',
            'message' => 'Hello from feedback.',
        ]);
    }

    public function test_admin_feedback_requires_admin_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.feedback.index'));

        $response->assertStatus(403);
    }

    public function test_admin_feedback_lists_messages(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        FeedbackMessage::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello admin.',
        ]);

        $response = $this->actingAs($user)->get(route('admin.feedback.index'));

        $response->assertOk();
        $response->assertSee('Hello admin.');
    }

    public function test_admin_can_mark_message_read_and_unread(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $message = FeedbackMessage::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello admin.',
            'is_read' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.feedback.read', $message->id))
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.feedback.unread', $message->id))
            ->assertRedirect();

        $this->assertDatabaseHas('feedback_messages', [
            'id' => $message->id,
            'is_read' => false,
        ]);
    }

    public function test_admin_can_delete_message(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $message = FeedbackMessage::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello admin.',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.feedback.destroy', $message->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('feedback_messages', [
            'id' => $message->id,
        ]);
    }

    public function test_admin_can_block_user_from_feedback(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create([
            'email' => 'blocked@example.com',
            'is_admin' => false,
            'is_blocked' => false,
        ]);
        $message = FeedbackMessage::create([
            'user_id' => $user->id,
            'name' => 'Blocked User',
            'email' => $user->email,
            'message' => 'Hello admin.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.feedback.block', $message->id))
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_blocked' => true,
        ]);
    }
}
