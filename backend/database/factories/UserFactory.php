<?php
// database/factories/UserFactory.php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'email'      => fake()->unique()->safeEmail(),
            'password'   => bcrypt('password'),
            'role'       => Role::Teacher,
            'is_blocked' => false,
            'phone'      => fake()->phoneNumber(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => Role::Admin]);
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['is_blocked' => true]);
    }
}
