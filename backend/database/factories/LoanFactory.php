<?php


namespace Database\Factories;

use App\Enums\MaterialStatus;
use App\Enums\ReturnStatus;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        $loanDate     = fake()->dateTimeBetween('-3 months', '-1 week');
        $expectedDate = fake()->dateTimeBetween($loanDate, '+1 month');

        return [
            'reservation_id'       => null,
            'user_id'              => User::factory()->state(['role' => Role::Teacher->value]),
            'material_id'          => Material::factory(),
            'loan_date'            => $loanDate->format('Y-m-d'),
            'expected_return_date' => $expectedDate->format('Y-m-d'),
            'actual_return_date'   => null,
            'return_status'        => null,
            'notes'                => fake()->optional(0.4)->sentence(),
        ];
    }

    /**
     * Loan returned in good condition.
     */
    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $returnDate = fake()->dateTimeBetween(
                $attributes['loan_date'],
                $attributes['expected_return_date']
            );
            return [
                'actual_return_date' => $returnDate->format('Y-m-d'),
                'return_status'      => ReturnStatus::Good->value,
            ];
        });
    }

    /**
     * Loan returned damaged.
     */
    public function returnedDamaged(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'actual_return_date' => fake()->dateTimeBetween(
                    $attributes['loan_date'],
                    'now'
                )->format('Y-m-d'),
                'return_status' => ReturnStatus::Damaged->value,
                'notes'         => 'Matériel retourné avec des dommages visibles.',
            ];
        });
    }

    /**
     * Loan marked as lost.
     */
    public function lost(): static
    {
        return $this->state(fn () => [
            'actual_return_date' => today()->format('Y-m-d'),
            'return_status'      => ReturnStatus::Lost->value,
            'notes'              => 'Matériel déclaré perdu.',
        ]);
    }

    /**
     * Ongoing loan (not yet returned, not overdue).
     */
    public function ongoing(): static
    {
        return $this->state(fn () => [
            'loan_date'            => today()->subDays(3)->format('Y-m-d'),
            'expected_return_date' => today()->addDays(7)->format('Y-m-d'),
            'actual_return_date'   => null,
            'return_status'        => null,
        ]);
    }

    /**
     * Overdue loan (past expected return date, not returned).
     */
    public function overdue(): static
    {
        return $this->state(fn () => [
            'loan_date'            => today()->subDays(20)->format('Y-m-d'),
            'expected_return_date' => today()->subDays(5)->format('Y-m-d'),
            'actual_return_date'   => null,
            'return_status'        => null,
            'notes'                => 'Retour en retard.',
        ]);
    }

    /**
     * Attach to an existing reservation.
     */
    public function forReservation(Reservation $reservation): static
    {
        return $this->state(fn () => [
            'reservation_id' => $reservation->id,
            'user_id'        => $reservation->user_id,
            'material_id'    => $reservation->material_id,
            'loan_date'      => $reservation->start_date->format('Y-m-d'),
            'expected_return_date' => $reservation->end_date->format('Y-m-d'),
        ]);
    }
}
