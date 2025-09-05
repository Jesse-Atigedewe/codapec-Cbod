<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CooperativeDistributionRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'dispatch_id',
        'cooperative_id',
        'quantity',
        'distributed_by',
        'distributed_at',
        'notes',
    ];
    public function dispatch() { return $this->belongsTo(Dispatch::class); }
    public function cooperative() { return $this->belongsTo(Cooperative::class); }
    public function distributor() { return $this->belongsTo(User::class, 'distributed_by'); }
}
