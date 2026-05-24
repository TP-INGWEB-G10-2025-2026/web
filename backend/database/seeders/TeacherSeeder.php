<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(5)->create();

        User::firstOrCreate(
            ['email' => 'teacher@teacher.com'],
            [
                'name'       => 'Super Teacher',
                'password'   => bcrypt('Teacher@12345'),
                'role'       => Role::Teacher,
                'is_blocked' => false,
                'phone'      => null,
            ]
        );
    }
}
