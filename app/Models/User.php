<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'second_name',
        'email',
        'password',
        'phone_number',
        'address',
        'zipcode',
        'avatar',
        'photo_path',
        'bio',
        'type',
        'is_blocked',
        'account_activated',
        'activation_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*********** Profile ***********/
    public function profile()
    {
        return $this->morphTo();
    }
}
