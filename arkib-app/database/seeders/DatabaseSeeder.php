<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@uitm.edu.my'],
            [
                'name' => 'SUPERADMIN',
                'password' => Hash::make('password'),
                'kampus' => 'UiTM',
                'cawangan' => null,
                'fakulti_bahagian' => null,
                'position' => null,
                'is_superadmin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
