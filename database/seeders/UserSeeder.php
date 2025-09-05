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
         User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'CODAPEC Rep',
            'email' => 'codapecrep@example.com',
            'password' => Hash::make('password'),
            'role' => 'codapecrep',
        ]);

        User::create([
            'name' => 'DCO User',
            'email' => 'dco@example.com',
            'password' => Hash::make('password'),
            'role' => 'dco',
        ]);

         User::create([
            'name' => 'Auditor User',
            'email' => 'auditor@example.com',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

          User::create([
            'name' => 'Regional Manager User',
            'email' => 'regionalmanager@example.com',
            'password' => Hash::make('password'),
            'role' => 'regional_manager',
        ]);
    }
}
