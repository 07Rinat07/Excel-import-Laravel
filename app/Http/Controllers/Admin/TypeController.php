<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TypeController extends Controller
{
    public function index(): Response
    {
        $types = Type::query()
            ->withCount(['projects', 'tasks', 'templates'])
            ->orderBy('title')
            ->get()
            ->map(function (Type $type) {
                return [
                    'id' => $type->id,
                    'title' => $type->title,
                    'description' => $type->description,
                    'projects_count' => $type->projects_count,
                    'tasks_count' => $type->tasks_count,
                    'templates_count' => $type->templates_count,
                ];
            });

        return Inertia::render('Admin/Types/Index', [
            'types' => $types,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80', Rule::unique('types', 'title')],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Type::create($data);

        return redirect()->back()->with(['message' => 'Type created.']);
    }

    public function update(Request $request, Type $type): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $type->update([
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->back()->with(['message' => 'Type updated.']);
    }

    public function destroy(Type $type): RedirectResponse
    {
        if ($type->projects()->exists() || $type->tasks()->exists() || $type->templates()->exists()) {
            return redirect()->back()->withErrors(['title' => 'Type is in use and cannot be deleted.']);
        }

        $type->delete();

        return redirect()->back()->with(['message' => 'Type deleted.']);
    }
}
