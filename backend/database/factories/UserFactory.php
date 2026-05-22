<?php
// database/factories/UserFactory.php

namespace Database\Factories;

<<<<<<< HEAD
use App\Models\User;
=======
use App\Enums\Role;
>>>>>>> dev
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
<<<<<<< HEAD
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
=======
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
>>>>>>> dev
    }
}
