<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Projecteurs',
            'Ordinateurs',
            'Capteurs & Arduino',
            'Marqueurs & Tableau',
            'Matériels de TP',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $this->command->info('✅ 5 catégories créées.');
    }
}
