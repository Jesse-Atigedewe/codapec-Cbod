<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RequestFarmer;
use App\Models\District;
use App\Models\Region;
use App\Models\FarmerGroup;
use App\Models\Cooperative;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'district_id',
        'operational_area',
        'farmer_id',
        'farmer_name',
        'contact_number',
        'id_card_number',
        'farm_location',
        'year_established',
        'farm_code',
        'cocoa_type',
        'hectares',
        'farm_size',
        'cooperative_id',
    ];


    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function farmerGroup()
    {
        return $this->belongsTo(FarmerGroup::class);
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }

     public function requests()
    {
        return $this->belongsToMany(Request::class, 'request_farmers')
                    ->withTimestamps();
    }

    public function requestFarmers()
    {
        return $this->hasMany(RequestFarmer::class);
    }
}
