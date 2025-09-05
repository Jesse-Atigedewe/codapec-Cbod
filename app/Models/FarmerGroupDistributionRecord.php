<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FarmerGroupDistributionRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'farmer_group_id',
        'quantity',
        'distributed_by',
        'distributed_at',
        'notes',
    ];
    public function dispatch() { return $this->belongsTo(Dispatch::class); }
    public function farmerGroup() { return $this->belongsTo(FarmerGroup::class); }
    public function distributor() { return $this->belongsTo(User::class, 'distributed_by'); }
}
