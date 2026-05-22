<?php


namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Material;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+1 month');
        $end   = fake()->dateTimeBetween($start, '+2 months');

        return [
            'user_id'          => User::factory()->state(['role' => Role::Teacher]),
            'material_id'      => Material::factory(),
            'start_date'       => $start->format('Y-m-d'),
            'end_date'         => $end->format('Y-m-d'),
            'status'           => ReservationStatus::Pending->value,
            'rejection_reason' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'           => ReservationStatus::Pending->value,
            'rejection_reason' => null,
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn () => [
            'status'           => ReservationStatus::Validated->value,
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'           => ReservationStatus::Rejected->value,
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status'           => ReservationStatus::Cancelled->value,
            'rejection_reason' => null,
        ]);
    }

    public function forDates(string $start, string $end): static
    {
        return $this->state(fn () => [
            'start_date' => $start,
            'end_date'   => $end,
        ]);
    }
}
