<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Type A',
            'Type B',
            'Type C',
        ];

        foreach ($types as $title) {
            Type::firstOrCreate(['title' => $title]);
        }
    }
}
