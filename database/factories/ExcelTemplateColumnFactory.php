<?php

namespace Database\Factories;

use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExcelTemplateColumnFactory extends Factory
{
    protected $model = ExcelTemplateColumn::class;

    public function definition(): array
    {
        $label = $this->faker->unique()->word();
        return [
            'template_id' => ExcelTemplate::factory(),
            'key' => Str::slug($label, '_'),
            'label' => ucfirst($label),
            'data_type' => 'string',
            'is_required' => false,
            'validation_rules' => null,
            'position' => 0,
        ];
    }
}
