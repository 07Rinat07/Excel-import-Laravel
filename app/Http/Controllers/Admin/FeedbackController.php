<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(): Response
    {
        $messages = FeedbackMessage::query()
            ->with('user')
            ->latest()
            ->paginate(15)
            ->through(function (FeedbackMessage $message) {
                $user = $message->user ?: User::where('email', $message->email)->first();

                return [
                    'id' => $message->id,
                    'name' => $message->name,
                    'email' => $message->email,
                    'message' => $message->message,
                    'is_read' => $message->is_read,
                    'user_id' => $message->user_id,
                    'user_blocked' => $user?->is_blocked ?? false,
                    'created_at' => $message->created_at?->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/Feedback/Index', [
            'messages' => $messages,
        ]);
    }

    public function markRead(FeedbackMessage $message): RedirectResponse
    {
        $message->update(['is_read' => true]);

        return redirect()->back()->with('message', 'Message marked as read.');
    }

    public function markUnread(FeedbackMessage $message): RedirectResponse
    {
        $message->update(['is_read' => false]);

        return redirect()->back()->with('message', 'Message marked as unread.');
    }

    public function blockUser(FeedbackMessage $message): RedirectResponse
    {
        $user = $message->user_id
            ? User::find($message->user_id)
            : User::where('email', $message->email)->first();

        if (! $user) {
            return redirect()->back()->with('message', 'User not found for this email.');
        }

        if ($user->is_admin) {
            return redirect()->back()->with('message', 'Admin users cannot be blocked.');
        }

        $user->update(['is_blocked' => true]);

        return redirect()->back()->with('message', 'User has been blocked.');
    }

    public function destroy(FeedbackMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->back()->with('message', 'Message deleted.');
    }
}
