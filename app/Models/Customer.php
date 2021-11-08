<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'birth_date',
        'orders_number',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class);
    }
}
