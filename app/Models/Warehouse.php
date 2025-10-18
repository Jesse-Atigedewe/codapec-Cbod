<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'region_id','district_id', 'name', 'location', 'description', ];

    public function dispatches() { return $this->hasMany(Dispatch::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function region() { return $this->belongsTo(Region::class); }
    public function district() { return $this->belongsTo(District::class); }
}
