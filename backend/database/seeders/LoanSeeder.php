<?php

namespace Database\Seeders;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Role;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $teachers  = User::where('role', Role::Teacher->value)->get();
        $materials = Material::all();

        if ($teachers->isEmpty()) {
            $teachers = User::factory()->count(3)->create(['role' => Role::Teacher->value]);
        }

        if ($materials->isEmpty()) {
            $category  = Category::first() ?? Category::factory()->create();
            $materials = Material::factory()->count(10)->create(['category_id' => $category->id]);
        }

        $teacher1 = $teachers->first();
        $teacher2 = $teachers->count() > 1 ? $teachers->get(1) : $teacher1;

        // ── Scenario 1: Returned in good condition (5 loans) ──
        Loan::factory()
            ->count(5)
            ->returned()
            ->create([
                'user_id'     => $teacher1->id,
                'material_id' => $materials->random()->id,
            ]);

        // ── Scenario 2: Returned damaged (2 loans) ────────────
        Loan::factory()
            ->count(2)
            ->returnedDamaged()
            ->create([
                'user_id'     => $teacher2->id,
                'material_id' => $materials->random()->id,
            ]);

        // ── Scenario 3: Lost material (1 loan) ────────────────
        Loan::factory()
            ->lost()
            ->create([
                'user_id'     => $teacher1->id,
                'material_id' => $materials->random()->id,
                'notes'       => 'Matériel introuvable après vérification.',
            ]);

        // ── Scenario 4: Ongoing loans (4 loans) ───────────────
        Loan::factory()
            ->count(4)
            ->ongoing()
            ->sequence(fn ($seq) => [
                'user_id'     => $teachers->random()->id,
                'material_id' => $materials->random()->id,
            ])
            ->create();

        // ── Scenario 5: Overdue loans (3 loans) ───────────────
        Loan::factory()
            ->count(3)
            ->overdue()
            ->sequence(fn ($seq) => [
                'user_id'     => $teachers->random()->id,
                'material_id' => $materials->random()->id,
            ])
            ->create();

        // ── Scenario 6: Loans linked to validated reservations ─
        $validatedReservations = Reservation::where('status', ReservationStatus::Validated->value)
            ->whereNotNull('material_id')
            ->limit(3)
            ->get();

        foreach ($validatedReservations as $reservation) {
            Loan::factory()
                ->forReservation($reservation)
                ->create();
        }

       
    }
}
