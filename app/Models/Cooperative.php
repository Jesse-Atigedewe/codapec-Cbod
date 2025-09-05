<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'region_id',
        'name',
        'leader_name',
        'leader_contact',
        'number_of_members'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
