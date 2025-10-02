<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
     use HasFactory;

     protected $fillable = [
          'user_id',
          'region_id',
          'district_id',
          'chemical_request_id',
          'driver_name',
          'driver_phone',
          'driver_license',
          'vehicle_number',
          'quantity',
          'trip_complete',
          'chemical_id',
          'status',
          'dispatched_at',
          'delivered_at',
          'dco_approved',
          'dco_approved_by',
          'dco_approved_at',
          'auditor_approved',
          'auditor_approved_by',
          'auditor_approved_at',
          'regional_manager_approved',
          'regional_manager_approved_by',
          'regional_manager_approved_at',
          'waybill',
          'notes',
     ];

     protected $casts = [
          'trip_complete' => 'boolean',
          'quantity' => 'integer',
          'dco_approved_at' => 'datetime',
          'auditor_approved_at' => 'datetime',
          'regional_manager_approved_at' => 'datetime',
     ];



     public function chemicalRequest()
     {
          return $this->belongsTo(ChemicalRequest::class);
     }
     public function user()
     {
          return $this->belongsTo(User::class);
     }
     public function warehouse()
     {
          return $this->belongsTo(Warehouse::class);
     }
     public function district()
     {
          return $this->belongsTo(District::class);
     }
     public function region()
     {
          return $this->belongsTo(Region::class);
     }
     public function chemical()
     {
          return $this->belongsTo(Chemical::class);
     }
     public function dcoApprover()
     {
          return $this->belongsTo(User::class, 'dco_approved_by');
     }
     public function auditorApprover()
     {
          return $this->belongsTo(User::class, 'auditor_approved_by');
     }
     public function regionalManagerApprover()
     {
          return $this->belongsTo(User::class, 'regional_manager_approved_by');
     }

}
