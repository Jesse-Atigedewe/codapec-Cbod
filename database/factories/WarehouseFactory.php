<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
                $userId = User::where('role', 'codapecrep')->inRandomOrder()->value('id');

         return [
            'user_id'     => $userId ?? User::factory()->state(['role' => 'codapecrep']),
            'name'        => $this->faker->company . ' Warehouse',
            'location'    => $this->faker->city,
            'description' => $this->faker->sentence(10),
        ];
    }
}
