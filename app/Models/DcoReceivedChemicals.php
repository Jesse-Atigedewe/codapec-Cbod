<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DcoReceivedChemicals extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_id',
        'dispatch_id',
        'user_id',
        'district_id',
        'region_id',
        'quantity_received',
        'quantity_distributed',
        'balance',
        'received_at',
    ];
    public function dispatch() { return $this->belongsTo(Dispatch::class); }
    public function receiver() { return $this->belongsTo(User::class, 'received_by'); }
    public function user() { return $this->belongsTo(User::class); }
    public function district() { return $this->belongsTo(District::class); }
    public function region() { return $this->belongsTo(Region::class); }

      public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
