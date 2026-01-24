<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackStoreRequest;
use App\Models\FeedbackMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Feedback/Create');
    }

    public function store(FeedbackStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        FeedbackMessage::create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
        ]);

        return redirect()
            ->route('feedback.create')
            ->with('message', 'Спасибо! Сообщение отправлено.');
    }
}
