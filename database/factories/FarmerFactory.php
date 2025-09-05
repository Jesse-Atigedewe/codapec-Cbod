<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farmer>
 */
class FarmerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $region = Region::inRandomOrder()->first();
        $district = $region?->districts()->inRandomOrder()->first();
        return [

            'region_id'       => $region?->id ?? Region::factory(),
            'district_id'     => $district?->id ?? District::factory(),
            'name'            => $this->faker->name,
            'contact_number'  => $this->faker->phoneNumber,
            'farm_size'       => $this->faker->numberBetween(1, 50) . ' acres',
        ];
    }
}
