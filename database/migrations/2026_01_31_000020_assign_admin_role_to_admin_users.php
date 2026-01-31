<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists(Role::class)) {
            return;
        }

        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        User::query()
            ->where('is_admin', true)
            ->get()
            ->each(function (User $user) use ($role) {
                if (! $user->hasRole($role->name)) {
                    $user->assignRole($role);
                }
            });
    }

    public function down(): void
    {
        if (! class_exists(Role::class)) {
            return;
        }

        $role = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if (! $role) {
            return;
        }

        User::query()
            ->where('is_admin', true)
            ->get()
            ->each(function (User $user) use ($role) {
                if ($user->hasRole($role->name)) {
                    $user->removeRole($role);
                }
            });

        $role->delete();
    }
};
