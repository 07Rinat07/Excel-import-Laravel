<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectValue;
use App\Models\ExcelTemplateColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectValueFactory extends Factory
{
    protected $model = ProjectValue::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'template_column_id' => ExcelTemplateColumn::factory(),
            'value' => $this->faker->sentence(2),
        ];
    }
}
