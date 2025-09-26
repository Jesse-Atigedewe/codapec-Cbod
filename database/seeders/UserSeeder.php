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
        User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::firstOrCreate([
            'email' => 'codapecrep@example.com',
        ], [
            'name' => 'CODAPEC Rep',
            'password' => Hash::make('password'),
            'role' => 'codapecrep',
        ]);

        User::firstOrCreate([
            'email' => 'dco@example.com',
        ], [
            'name' => 'DCO User',
            'password' => Hash::make('password'),
            'role' => 'dco',
        ]);

        User::firstOrCreate([
            'email' => 'auditor@example.com',
        ], [
            'name' => 'Auditor User',
            'password' => Hash::make('password'),
            'role' => 'auditor',
        ]);

        User::firstOrCreate([
            'email' => 'regionalmanager@example.com',
        ], [
            'name' => 'Regional Manager User',
            'password' => Hash::make('password'),
            'role' => 'regional_manager',
        ]);
    }
}
