<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffDismissal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_uuid',
        'dismissal_reason',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_uuid', 'uuid');
    }
}
