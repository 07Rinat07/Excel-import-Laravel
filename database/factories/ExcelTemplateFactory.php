<?php

namespace Database\Factories;

use App\Models\ExcelTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExcelTemplateFactory extends Factory
{
    protected $model = ExcelTemplate::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Template',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
