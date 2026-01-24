<?php

namespace Database\Seeders;

use App\Models\FailedRow;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password'), 'is_admin' => true]
        );

        $type = Type::firstOrCreate(['title' => 'Type A']);

        $project = Project::create([
            'type_id' => $type->id,
            'title' => 'Demo Project',
            'created_at_time' => now()->subDays(10)->toDateString(),
            'contracted_at' => now()->subDays(5)->toDateString(),
            'deadline' => now()->addDays(30)->toDateString(),
            'is_chain' => false,
            'is_on_time' => true,
            'has_outsource' => false,
            'has_investors' => false,
            'worker_count' => 5,
            'service_count' => 2,
            'payment_first_step' => 1000,
            'payment_second_step' => 2000,
            'payment_third_step' => 1500,
            'payment_forth_step' => 500,
            'comment' => 'Seeded demo project.',
            'effective_value' => 1.25,
        ]);

        $file = File::create([
            'path' => 'files/demo.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'demo.xlsx',
        ]);

        $task = Task::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'status' => Task::STATUS_ERROR,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        FailedRow::create([
            'key' => 'Type',
            'row' => 2,
            'message' => 'Demo validation error.',
            'task_id' => $task->id,
        ]);

        $project->touch();
    }
}
