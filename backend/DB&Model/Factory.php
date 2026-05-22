<?php
namespace Database\Factories;

use App\Enums\MaterialStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(MaterialStatus::cases()),
            'description' => $this->faker->sentence(),
        ];
    }
}
?>