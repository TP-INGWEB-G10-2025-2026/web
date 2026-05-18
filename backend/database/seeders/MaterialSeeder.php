<?php


namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $categories = Category::factory()->count(5)->create();
        }

        // 20 materials distributed across existing categories
        Material::factory()
            ->count(20)
            ->sequence(fn ($seq) => [
                'category_id' => $categories->random()->id,
            ])
            ->create();
    }
}
