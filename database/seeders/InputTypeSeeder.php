<?php

namespace Database\Seeders;

use App\Models\ChemicalType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InputTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $regions = [
            'Fungicides',
            'Insecticides',
            'Granular Fertilizer',
            'Liquid Fertilizer',
            'Flower Inducer',
        ];

        foreach ($regions as $region) {
            ChemicalType::firstOrCreate(['name' => $region]);
        }
    }
}
