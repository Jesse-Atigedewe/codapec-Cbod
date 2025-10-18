<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $regions = [
            'BRONG-AHAFO',
            'CENTRAL',
            'EASTERN',
            'ASHANTI',
            'WESTERN-NORTH',
            'VOLTA',
            'WESTERN-SOUTH',
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(['name' => $region]);
        }
    }
}
