<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User WITH permissions
        User::updateOrCreate(
            ['email' => 'user@auren-con-permiso.com'],
            [
                'name' => 'user@auren-con-permiso.com',
                'password' => Hash::make('password'),
            ]
        );

        // User WITHOUT permissions
        User::updateOrCreate(
            ['email' => 'user@auren-sin-permiso.com'],
            [
                'name' => 'user@auren-sin-permiso.com',
                'password' => Hash::make('password'),
            ]
        );
    }
}
