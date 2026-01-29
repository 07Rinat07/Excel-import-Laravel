<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'file_id' => File::factory(),
            'status' => Task::STATUS_PENDING,
            'type' => 1,
            'type_id' => Type::factory(),
            'template_id' => null,
            'column_map' => null,
        ];
    }
}
