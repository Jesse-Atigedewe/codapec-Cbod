<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'district_id',
        'name',
        'leader_name',
        'leader_contact',
    'number_of_members',
    'dco_received_chemical_id'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function farmers()
    {
        return $this->hasMany(Farmer::class);
    }

    public function cooperative()
    {
        return $this->belongsTo(Cooperative::class);
    }
}
