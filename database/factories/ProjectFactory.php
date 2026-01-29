<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Models\ExcelTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $date = $this->faker->date();

        return [
            'type_id' => Type::factory(),
            'task_id' => Task::factory(),
            'template_id' => ExcelTemplate::factory(),
            'title' => $this->faker->sentence(3),
            'created_at_time' => $date,
            'contracted_at' => $date,
            'deadline' => null,
        ];
    }
}
