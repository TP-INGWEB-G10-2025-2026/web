<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $teachers  = User::where('role', 'teacher')->get();
        $materials = Material::where('status', 'available')->get();

        if ($teachers->isEmpty() || $materials->isEmpty()) {
            $this->command->warn('⚠️  Aucun enseignant ou matériel disponible pour le seeder.');
            return;
        }

        $teacher1 = $teachers->first();
        $teacher2 = $teachers->count() > 1 ? $teachers->get(1) : $teacher1;
        $material = $materials->first();

        // Scénario 1 — Demande en attente (future)
        Reservation::create([
            'user_id'    => $teacher1->id,
            'material_id'=> null,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date'   => now()->addDays(10)->toDateString(),
            'status'     => Reservation::STATUS_PENDING,
        ]);

        // Scénario 2 — Demande validée avec matériel assigné
        Reservation::create([
            'user_id'     => $teacher2->id,
            'material_id' => $material->id,
            'start_date'  => now()->addDays(15)->toDateString(),
            'end_date'    => now()->addDays(20)->toDateString(),
            'status'      => Reservation::STATUS_VALIDATED,
        ]);

        // Scénario 3 — Demande rejetée avec raison
        Reservation::create([
            'user_id'          => $teacher1->id,
            'material_id'      => null,
            'start_date'       => now()->subDays(5)->toDateString(),
            'end_date'         => now()->subDays(2)->toDateString(),
            'status'           => Reservation::STATUS_REJECTED,
            'rejection_reason' => 'Matériel non disponible sur cette période.',
        ]);

        // Scénario 4 — Demande annulée
        Reservation::create([
            'user_id'    => $teacher2->id,
            'material_id'=> null,
            'start_date' => now()->addDays(25)->toDateString(),
            'end_date'   => now()->addDays(30)->toDateString(),
            'status'     => Reservation::STATUS_CANCELLED,
        ]);

        $this->command->info('✅ 4 réservations de test créées (pending, validated, rejected, cancelled).');
    }
}
