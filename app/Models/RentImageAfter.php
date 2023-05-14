<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentImageAfter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rent_order_id',
        'photo_rear',
        'photo_front',
        'photo_side',
    ];

    protected $appends = [
        'photo_rear_url',
        'photo_front_url',
        'photo_side_url',
    ];

    /**
     * Get the rent order that owns the image.
     */
    public function rentOrder()
    {
        return $this->belongsTo(RentOrder::class);
    }

    /**
     * Get the URL for the photo_rear attribute.
     *
     * @return string
     */
    public function getPhotoRearUrlAttribute()
    {
        if ($this->photo_rear) {
            return asset('storage/' . $this->photo_rear);
        }

        return null;
    }

    /**
     * Get the URL for the photo_front attribute.
     *
     * @return string
     */
    public function getPhotoFrontUrlAttribute()
    {
        if ($this->photo_front) {
            return asset('storage/' . $this->photo_front);
        }

        return null;
    }

    /**
     * Get the URL for the photo_side attribute.
     *
     * @return string
     */
    public function getPhotoSideUrlAttribute()
    {
        if ($this->photo_side) {
            return asset('storage/' . $this->photo_side);
        }

        return null;
    }
}
