<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'type_id',
    'formula_quantity',
    'unit',
];

    public function type()  
    {
        return $this->belongsTo(ChemicalType::class, 'type_id');
    }

}


