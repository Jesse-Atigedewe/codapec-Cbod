<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'warehouse_id',
        'chemical_id', 
        'quantity_received', 
        'quantity_available',
        'batch_number',
        'received_date'
    ];

    public function casts()
    {
        return [
            'quantity_received' => 'integer',
            'quantity_available' => 'integer',
            'received_date' => 'date',
        ];
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
