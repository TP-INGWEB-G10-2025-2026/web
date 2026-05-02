<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@univ-ndere.cm'],
            [
                'name'       => 'Administrateur GMP',
                'password'   => Hash::make('Admin@1234'),
                'role'       => 'admin',
                'is_blocked' => false,
                'phone'      => '+237600000000',
            ]
        );
    }
}
