<?php

namespace Database\Seeders;

use App\Models\FarmerGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                FarmerGroup::factory()->count(45)->create();

    }
}
