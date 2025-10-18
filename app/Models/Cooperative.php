<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'district_id',
        'name',
        'registration_number',
        'leader_name',
        'leader_contact',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function farmerGroups()
    {
        return $this->hasMany(FarmerGroup::class);
    }

    public function farmers()
    {
        return $this->hasMany(Farmer::class);
    }
}
