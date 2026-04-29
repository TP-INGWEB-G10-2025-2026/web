<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;


class User extends Model
{
    use HasApiTokens, SoftDeletes, HasFactory;
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'is_blocked',
        'profile_picture',
        'phone'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'role' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isBlocked()
    {
        return $this->is_blocked === true;
    }
}
