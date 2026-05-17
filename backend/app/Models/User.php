<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\Role;
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
        'role' => Role::class,
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
        return $this->role === Role::Admin;
    }

    public function isTeacher()
    {
        return $this->role === Role::Teacher;
    }

    public function isBlocked()
    {
        return $this->is_blocked === true;
    }

    public function block()
    {
        $this->is_blocked = true;
        $this->save();
    }

    public function unblock()
    {
        $this->is_blocked = false;
        $this->save();
    }
}
