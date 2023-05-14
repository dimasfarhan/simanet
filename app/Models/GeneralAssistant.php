<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class GeneralAssistant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_uuid',
        'uuid',
        'name',
        'nip',
        'phone',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'religion',
        'address',
        'is_active',
        'date_joined',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_joined' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeResign($query)
    {
        return $query->whereHas('staffDismissal', function ($q) {
            $q->where('dismissal_reason', 'resign');
        });
    }

    public function staffDismissal()
    {
        return $this->hasOne(StaffDismissal::class, 'staff_uuid', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
