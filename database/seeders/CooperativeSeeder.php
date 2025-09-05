<?php

namespace Database\Seeders;

use App\Models\Cooperative;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CooperativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                Cooperative::factory()->count(30)->create(); // create 30 cooperatives

    }
}
