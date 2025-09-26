<?php

namespace Database\Seeders;

use App\Models\Dispatch;
use App\Models\ChemicalRequest;
use App\Models\Chemical;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Seeder;

class DispatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chemical = Chemical::first() ?? Chemical::firstOrCreate(['name' => 'Sample Chemical']);

        // ensure a seed user exists
        $user = User::where('role', 'codapecrep')->first() ?? User::firstOrCreate(['email' => 'seeduser@example.com'], ['name' => 'Seed User', 'password' => '\$2y\$10\$examplehash', 'role' => 'codapecrep']);

        // Ensure basic region/district/warehouse exist by using firstOrCreate on their tables
        $region = \App\Models\Region::first() ?? \App\Models\Region::firstOrCreate(['name' => 'Central']);
        $district = \App\Models\District::first() ?? \App\Models\District::firstOrCreate(['name' => 'Central District', 'region_id' => $region->id]);
    $owner = User::where('role', 'codapecrep')->first() ?? User::firstOrCreate(['email' => 'warehouse_owner@example.com'], ['name' => 'Warehouse Owner', 'password' => '\$2y\$10\$examplehash', 'role' => 'codapecrep']);
    $warehouse = Warehouse::first() ?? Warehouse::firstOrCreate(['name' => 'Main Warehouse'], ['location' => 'Unknown', 'user_id' => $owner->id]);

        // create a chemical request to attach dispatches to (idempotent)
        $chemicalRequest = ChemicalRequest::firstOrCreate([
            'chemical_id' => $chemical->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user->id,
        ], [
            'region_id' => $region->id,
            'district_id' => $district->id,
            'warehouse_rep_id' => $owner->id,
            'quantity' => 500,
            'status' => 'approved',
        ]);

        // Use region/district/chemical from the chemical request if available
        $regionId = $chemicalRequest->region_id;
        $districtId = $chemicalRequest->district_id;
        $chemicalId = $chemicalRequest->chemical_id ?? $chemical->id;

        // create a couple of dispatches for the same chemical request
        Dispatch::create([
            'chemical_request_id' => $chemicalRequest->id,
            'user_id' => $user->id,
            'region_id' => $regionId,
            'district_id' => $districtId,
            'chemical_id' => $chemicalId,
            'driver_name' => 'Alice Driver',
            'driver_phone' => '0711000001',
            'driver_license' => 'DL-0001',
            'vehicle_number' => 'KBA-001A',
            'quantity' => 100,
            'trip_complete' => false,
        ]);

        Dispatch::create([
            'chemical_request_id' => $chemicalRequest->id,
            'user_id' => $user->id,
            'region_id' => $regionId,
            'district_id' => $districtId,
            'chemical_id' => $chemicalId,
            'driver_name' => 'Bob Driver',
            'driver_phone' => '0711000002',
            'driver_license' => 'DL-0002',
            'vehicle_number' => 'KBA-002B',
            'quantity' => 150,
            'trip_complete' => false,
        ]);
    }
}
