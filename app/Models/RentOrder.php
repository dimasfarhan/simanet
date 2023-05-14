<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentOrder extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_WAITING_FOR_APPROVAL = 'WAITING_FOR_APPROVAL';
    const STATUS_APPROVED_BY_GA = 'APPROVED_BY_GA';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECT = 'REJECT';
    const STATUS_DONE = 'DONE';

    protected $fillable = [
        'vehicle_id',
        'user_uuid',
        'ga_uuid',
        'ga_approved_at',
        'bod_uuid',
        'bod_approved_at',
        'status',
        'rented_at',
        'returned_at'
    ];

    public function scopeStatus($query, $status)
    {
        return $query->whereIn('status', $status);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUBMITTED,
            self::STATUS_WAITING_FOR_APPROVAL,
            self::STATUS_APPROVED_BY_GA,
            self::STATUS_APPROVED,
            self::STATUS_REJECT,
            self::STATUS_DONE,
        ];
    }

    public function setStatusAttribute($value)
    {
        if (!in_array($value, self::getStatuses())) {
            throw new InvalidArgumentException("Invalid status value: $value");
        }

        $this->attributes['status'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'];
    }

    public function rentImageBefore()
    {
        return $this->hasOne(RentImageBefore::class, 'rent_order_id', 'id');
    }

    public function rentImageAfter()
    {
        return $this->hasOne(RentImageAfter::class, 'rent_order_id', 'id');
    }

    public function rentReject()
    {
        return $this->hasOne(RentOrderReject::class, 'rent_order_id', 'id');
    }

    /**
     * Get the user that owns the rent order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * Get the general assistant that owns the rent order.
     */
    public function generalAssistant()
    {
        return $this->belongsTo(GeneralAssistant::class, 'ga_uuid', 'uuid');
    }

    /**
     * Get the board of director that owns the rent order.
     */
    public function boardOfDirector()
    {
        return $this->belongsTo(BoardOfDirector::class, 'bod_uuid', 'uuid');
    }

    /**
     * Get the vehicle that belongs to the rent order.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
