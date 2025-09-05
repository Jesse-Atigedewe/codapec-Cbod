<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FarmerDistributionRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'farmer_id',
        'quantity',
        'distributed_by',
        'distributed_at',
        'notes',
        'dco_received_chemical_id',
    ];
    public function dispatch() { return $this->belongsTo(Dispatch::class); }
    public function farmer() { return $this->belongsTo(Farmer::class); }
    public function dcoReceivedChemical()
{
    return $this->belongsTo(DcoReceivedChemicals::class);
}

    
    public function distributor() { return $this->belongsTo(User::class, 'distributed_by'); }
}
