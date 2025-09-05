<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
        use HasFactory;

     protected $fillable = ['name', 'description'];

    public function districts()
    {
        return $this->hasMany(District::class);
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
