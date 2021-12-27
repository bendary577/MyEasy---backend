<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'birth_date',
        'delivery_speed',
        'has_store',
        'badge',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function national_identity()
    {
        return $this->hasOne(File::class);
    }

}
