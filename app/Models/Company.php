<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'customers_number',
        'orders_number',
        'badge',
        'delivery_speed',
        'has_store',
        'specilize',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class);
    }
}
