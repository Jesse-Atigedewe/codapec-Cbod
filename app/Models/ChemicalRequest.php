<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChemicalRequest extends Model
{
use HasFactory;

    
    protected $fillable = [
        'region_id',
        'district_id',
        'user_id',
        'chemical_id',
        'haulage_company_id',
        'warehouse_rep_id',   
        'warehouse_id',
        'quantity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dispatches()
{
    return $this->hasMany(\App\Models\Dispatch::class);
}


    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class,'chemical_id');
    }

    public function haulageCompany(): BelongsTo
    {
        return $this->belongsTo(HaulageCompany::class);
    }

    public function dispatch()
    {
        return $this->hasOne(\App\Models\Dispatch::class, 'chemical_request_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }


    public static function statuses(): array
    {
        return ['pending', 'approved', 'dispatched'];
    }

      // ✅ Total dispatched so far
    public function getDispatchedQuantityAttribute(): float
    {
        return $this->dispatches->sum(
            fn($dispatch) => (int) ($dispatch->quantity ?? 0)
        );
    }

    // ✅ Remaining balance
    public function getRemainingQuantityAttribute(): float
    {
        return $this->quantity - $this->dispatched_quantity;
    }

    
    
}
