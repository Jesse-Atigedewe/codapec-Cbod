<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'district_id',
        'region_id',
        'cooperative_id',
        'chemical_id',
        'status',
        'regional_manager_approved',
        'admin_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function dispatches()
{
    return $this->hasMany(Dispatch::class);
}


 public function dcoreceivedchemical(){
    return $this->belongsTo(DcoReceivedChemicals::class);
 }


    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }
public function request()
{
    return $this->belongsTo(\App\Models\Request::class);
}

    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }




    public function farmers()
    {
        return $this->belongsToMany(Farmer::class, 'request_farmers',)
            ->withPivot('allocated_quantity');
    }

    public function requestFarmers()
    {
        return $this->hasMany(RequestFarmer::class);
    }


    public function chemicalRequests()
{
    return $this->hasMany(\App\Models\ChemicalRequest::class);
}




    public function rejectedByRegionalManager()
    {
        $this->status = 'rejected';
        $this->regional_manager_approved = false;
        $this->save();
        $this->removeChemicalAllocations();
    }

    public function rejectedByAdmin()
    {
        $this->status = 'rejected';
        $this->admin_approved = false;
        $this->save();
        $this->removeChemicalAllocations();
    }


    //calculate and allocate chemical quantities to farmers based on their farm size
    public function allocateChemicalToFarmers(): void
    {
        $chemical = $this->chemical;

        if (! $chemical || ! $chemical->formula_quantity) {
            return;
        }

        foreach ($this->farmers as $farmer) {
            $quantity = $farmer->hectares * $chemical->formula_quantity;

            // ✅ Only allocate if not already done
            $existing = $this->farmers()
                ->wherePivot('farmer_id', $farmer->id)
                ->wherePivot('allocated_quantity', '>', 0)
                ->exists();

            if (! $existing) {
                $this->farmers()->updateExistingPivot($farmer->id, [
                    'allocated_quantity' => $quantity,
                ]);
            }
        }
    }
    public function removeChemicalAllocations(): void
{
    $this->farmers()->detach();
}



//create chemical request when admin approves
public function createChemicalRequest(int $warehouseId, int $haulageCompanyId): void
{
    // Get related data
    $user = $this->user; // user who created the request;
    $regionId = $user->region_id;
    $districtId = $user->district_id;
    $warehouse = \App\Models\Warehouse::find($warehouseId);

    // Safety checks
    if (! $warehouse || !$warehouse->user_id || ! $regionId || ! $districtId || ! $this->chemical_id) {
        Log::warning("Cannot create ChemicalRequest for Request {$this->id} due to missing info.");
        return;
    }




    // Calculate total quantity allocated to farmers
    $totalAllocated = $this->farmers()->sum('request_farmers.allocated_quantity');

    // Avoid duplicates
    if ($this->chemicalRequest()->exists()) {
        Log::info("ChemicalRequest already exists for Request {$this->id}");
        return;
    }

    // Create Chemical Request
        ChemicalRequest::create([
        'request_id' => $this->id,
        'region_id' => $regionId,
        'district_id' => $districtId,
        'user_id' => $this->user_id,
        'warehouse_rep_id' => $warehouse->user_id,
        'chemical_id' => $this->chemical_id,
        'haulage_company_id' => $haulageCompanyId,
        'warehouse_id' => $warehouseId,
        'quantity' => $totalAllocated,
        'status' => 'pending',
    ]);
}

// Relationship to ChemicalRequest
public function chemicalRequest()
{
    return $this->hasOne(\App\Models\ChemicalRequest::class);
}



}
