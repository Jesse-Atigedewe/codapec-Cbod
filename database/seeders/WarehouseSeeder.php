<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Region;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get any admin or first user to assign warehouses to
        $user = User::where('role', 'admin')->first() ?? User::first();

        // Define the warehouses
        $warehouses = [
            [
                'name' => 'Spintex',
                'location_name' => 'Spintex',
                'region' => null,
                'district' => null,
            ],
            [
                'name' => 'Nsawam',
                'location_name' => 'Nsawam',
                'region' => null,
                'district' => null,
            ],
            [
                'name' => 'Swedru Central',
                'location_name' => null,
                'region' => 'CENTRAL',
                'district' => null, // example district in Central region
            ],
            [
                'name' => 'Koforidua',
                'location_name' => null,
                'region' => 'EASTERN',
                'district' => null,
            ],
            [
                'name' => 'Abukwa - Kumasi',
                'location_name' => null,
                'region' => 'ASHANTI',
                'district' => null,
            ],
            [
                'name' => 'Asokwa - Kumasi',
                'location_name' => null,
                'region' => 'ASHANTI',
                'district' => null,
            ],
            [
                'name' => 'Kaase - Kumasi',
                'location_name' => null,
                'region' => 'ASHANTI',
                'district' => null,
            ],
        ];

        foreach ($warehouses as $data) {
            $region = null;
            $district = null;

            // Find region if provided
            if (!empty($data['region'])) {
                $region = Region::where('name', strtoupper($data['region']))->first();
            }

            // Find district if provided
            if (!empty($data['district'])) {
                $district = District::where('name', 'LIKE', "%{$data['district']}%")->first();
            }

            Warehouse::create([
                'user_id' => $user?->id ?? 1,
                'region_id' => $region?->id,
                'district_id' => $district?->id,
                'name' => $data['name'],
                'location_name' => $data['location_name'],
                'description' => "Warehouse located at {$data['name']}",
            ]);
        }
    }
}
