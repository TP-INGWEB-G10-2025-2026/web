<?php


namespace Database\Factories;

use App\Enums\MaterialStatus;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->randomElement([
                'Ordinateur portable', 'Projecteur', 'Tableau blanc',
                'Imprimante', 'Scanner', 'Caméra', 'Microphone',
                'Clavier', 'Souris', 'Moniteur', 'Disque dur externe',
                'Câble HDMI', 'Switch réseau', 'Routeur', 'Tablette',
                'Webcam', 'Casque audio', 'Enceinte Bluetooth',
                'Chargeur universel', 'Hub USB',
            ]) . ' #' . fake()->numberBetween(1, 99),
            'category_id' => Category::factory(),
            'status'      => fake()->randomElement(MaterialStatus::cases())->value,
            'description' => fake()->optional(0.7)->sentence(),
        ];
    }

    public function available(): static
    {
        return $this->state(fn () => ['status' => MaterialStatus::Available->value]);
    }

    public function broken(): static
    {
        return $this->state(fn () => ['status' => MaterialStatus::Broken->value]);
    }
}
