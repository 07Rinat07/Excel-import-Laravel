<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->through(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'is_blocked' => $user->is_blocked,
                    'created_at' => $user->created_at?->format('Y-m-d H:i'),
                ];
            })
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'q' => $search,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            $name = Str::of($data['email'])->before('@')->toString();
        }

        User::create([
            'name' => $name,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => (bool) ($data['is_admin'] ?? false),
            'is_blocked' => false,
        ]);

        return redirect()->back()->with('message', 'User created.');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        if ($this->isSelf($request, $user)) {
            return redirect()->back()->with('message', 'You cannot block your own account.');
        }

        if ($user->is_admin) {
            return redirect()->back()->with('message', 'Admin users cannot be blocked.');
        }

        $user->update(['is_blocked' => true]);

        return redirect()->back()->with('message', 'User blocked.');
    }

    public function unblock(User $user): RedirectResponse
    {
        $user->update(['is_blocked' => false]);

        return redirect()->back()->with('message', 'User unblocked.');
    }

    public function makeAdmin(User $user): RedirectResponse
    {
        $user->update(['is_admin' => true]);

        return redirect()->back()->with('message', 'User promoted to admin.');
    }

    public function revokeAdmin(Request $request, User $user): RedirectResponse
    {
        if ($this->isSelf($request, $user)) {
            return redirect()->back()->with('message', 'You cannot revoke your own admin role.');
        }

        if ($this->isLastAdmin($user)) {
            return redirect()->back()->with('message', 'At least one admin must remain.');
        }

        $user->update(['is_admin' => false]);

        return redirect()->back()->with('message', 'Admin role revoked.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($this->isSelf($request, $user)) {
            return redirect()->back()->with('message', 'You cannot delete your own account.');
        }

        if ($this->isLastAdmin($user)) {
            return redirect()->back()->with('message', 'At least one admin must remain.');
        }

        $user->delete();

        return redirect()->back()->with('message', 'User deleted.');
    }

    private function isSelf(Request $request, User $user): bool
    {
        return $request->user()->id === $user->id;
    }

    private function isLastAdmin(User $user): bool
    {
        if (! $user->is_admin) {
            return false;
        }

        return User::query()->where('is_admin', true)->count() <= 1;
    }
}
