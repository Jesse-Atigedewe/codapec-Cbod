<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cooperative>
 */
class CooperativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // grab a random region and district
        $region = Region::inRandomOrder()->first();
        $district = $region?->districts()->inRandomOrder()->first();
        return [
            'region_id'          => $region?->id ?? Region::factory(),
            'district_id'        => $district?->id ?? District::factory(),
            'name'               => $this->faker->company . ' Cooperative',
            'leader_name'        => $this->faker->name,
            'leader_contact'     => $this->faker->phoneNumber,
            'number_of_members'  => $this->faker->numberBetween(10, 300),
        ];
    }
}
