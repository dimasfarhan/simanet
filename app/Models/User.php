<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the roles that the user has.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function child()
    {
        $role = $this->roles()->first()->name;
        if ($role == 'bod') {
            return $this->hasOne(BoardOfDirector::class, 'user_uuid', 'uuid');
        } else if ($role == 'ga') {
            return $this->hasOne(GeneralAssistant::class, 'user_uuid', 'uuid');
        }

        return $this->hasOne(Staff::class, 'user_uuid', 'uuid');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param  string  $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }
}
