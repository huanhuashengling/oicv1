<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Model implements AuthenticatableContract, CanResetPasswordContract, JWTSubject
{
    use HasFactory;
    use Authenticatable, CanResetPassword;
    
    protected $fillable = [
        'username', 'email', 'gender', 'sclasses_id', 'level', 'score', 'password', 'groups_id', 'order_in_group', 'remember_token', 'is_lock', 'work_comment_enable', 'work_max_num'
        ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
