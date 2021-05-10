<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    const ADMIN_USER = 'admin';
    const MEMBER_USER = 'member';
    
    use HasFactory, Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function animals()
    {
        return $this->hasMany('App\Models\Animal','user_id','id');
    }

    public function isAdmin()
    {
        return $this->permission === User::ADMIN_USER;
    }

    /**
     *  many to many relation animal and user likes
     */
    public function likes()
    {
        return $this->belongsToMany('App\Models\Animal','animal_user_likes')->withTimestamps();
    }
}
