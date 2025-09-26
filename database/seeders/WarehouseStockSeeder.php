<?php

namespace Database\Seeders;

use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\Chemical;
use App\Models\User;
use Illuminate\Database\Seeder;

class WarehouseStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // ensure there's a user to own the warehouse
    $owner = User::where('role', 'codapecrep')->first() ?? User::firstOrCreate(['email' => 'warehouse_owner@example.com'], ['name' => 'Warehouse Owner', 'password' => '\$2y\$10\$examplehash', 'role' => 'codapecrep']);
    $warehouse = Warehouse::first() ?? Warehouse::firstOrCreate(['name' => 'Main Warehouse'], ['location' => 'Unknown', 'user_id' => $owner->id]);
    $chemical = Chemical::first() ?? Chemical::firstOrCreate(['name' => 'Sample Chemical']);
    $user = User::first() ?? User::firstOrCreate(['email' => 'seeduser@example.com'], ['name' => 'Seed User', 'password' => '\$2y\$10\$examplehash', 'role' => 'codapecrep']);

        // create a few warehouse stocks with and without driver/vehicle info
        WarehouseStock::create([
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
            'chemical_id' => $chemical->id,
            'quantity_received' => 100,
            'quantity_available' => 100,
            'batch_number' => 'BATCH-001',
            'received_date' => now(),
            'driver_name' => null,
            'driver_phone' => null,
            'driver_license' => null,
            'vehicle_number' => null,
        ]);

        WarehouseStock::create([
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
            'chemical_id' => $chemical->id,
            'quantity_received' => 200,
            'quantity_available' => 200,
            'batch_number' => 'BATCH-002',
            'received_date' => now(),
            'driver_name' => 'John Doe',
            'driver_phone' => '0712345678',
            'driver_license' => 'DL-12345',
            'vehicle_number' => 'KAA-123A',
        ]);
    }
}
