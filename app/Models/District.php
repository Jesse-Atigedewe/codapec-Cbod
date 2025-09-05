<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
     use HasFactory;

    protected $fillable = ['region_id', 'name', 'description'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function farmers()
    {
        return $this->hasMany(Farmer::class);
    }

    public function farmerGroups()
    {
        return $this->hasMany(FarmerGroup::class);
    }

    public function cooperatives()
    {
        return $this->hasMany(Cooperative::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
