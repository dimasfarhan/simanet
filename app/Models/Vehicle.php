<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'jenis_kendaraan',
        'nomor_polisi',
        'brand',
        'model_type',
        'status',
    ];

    public function vehicleAccident()
    {
        return $this->hasMany(VehicleAccident::class, 'vehicle_id', 'id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'AVAILABLE');
    }

    public function scopeUnavailable($query)
    {
        return $query->where('status', 'UNAVAILABLE');
    }

    public function scopeRented($query)
    {
        return $query->where('status', 'RENTED');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'MAINTENANCE');
    }
}
