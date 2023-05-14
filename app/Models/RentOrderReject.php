<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentOrderReject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rent_order_id',
        'rejected_by',
        'reason'
    ];

    public function rentOrder()
    {
        return $this->belongsTo(RentOrder::class);
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'uuid');
    }
}
