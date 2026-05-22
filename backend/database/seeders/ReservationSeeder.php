<?php


namespace Database\Seeders;

use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
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
        $mat1     = $materials->first();
        $mat2     = $materials->count() > 1 ? $materials->get(1) : $mat1;


        Reservation::factory()->count(5)->pending()->create([
            'user_id' => $teacher1->id,
        ]);


        Reservation::factory()->count(4)->validated()->create([
            'user_id'     => $teacher2->id,
            'material_id' => $mat1->id,
        ]);


        Reservation::factory()->count(3)->rejected()->create([
            'user_id'          => $teacher1->id,
            'material_id'      => $mat2->id,
            'rejection_reason' => 'Matériel indisponible pour cette période.',
        ]);


        Reservation::factory()->count(2)->cancelled()->create([
            'user_id' => $teacher2->id,
        ]);


        Reservation::factory()->validated()->forDates('2025-06-01', '2025-06-10')->create([
            'user_id'     => $teacher1->id,
            'material_id' => $mat1->id,
        ]);

        Reservation::factory()->pending()->forDates('2025-05-15', '2025-06-15')->create([
            'user_id'     => $teacher2->id,
            'material_id' => $mat1->id,
        ]);

    }
}
