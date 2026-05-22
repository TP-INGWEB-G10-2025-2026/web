<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'       => 'Super Admin',
                'password'   => bcrypt('Admin@12345'),
                'role'       => Role::Admin,
                'is_blocked' => false,
                'phone'      => null,
            ]
        );
    }
}
