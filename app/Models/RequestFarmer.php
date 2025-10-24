<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Request;
use App\Models\Farmer;


class RequestFarmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'farmer_id',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }
}
