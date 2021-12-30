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
        'name',
        'username',
        'email',
        'password',
        'phone',
        'address',
        'zipcode',
        'bio',
        'blocked',
        'account_activated',
        'account_activated_at',
        'activation_token',
        'available_money_amnt',
        'forgot_password_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'account_activated_at' => 'datetime',
    ];

    protected $with = ['profile'];

    public function profile()
    {
        return $this->morphTo();
    }

    public function getHasAdminProfileAttribute()
    {
      return $this->profile_type == 'App\Models\Admin';
    }

    public function getHasCustomerProfileAttribute()
    {
      return $this->profile_type == 'App\Models\Customer';
    }

    public function getHasSellerProfileAttribute()
    {
      return $this->profile_type == 'App\Models\Seller';
    }
    
    public function getHasCompanyProfileAttribute()
    {
      return $this->profile_type == 'App\Models\Company';
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function avatar()
    {
        return $this->hasOne(Avatar::class);
    }
}
