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
        return $this->hasMany(SellerInvoice::class);
    }

    public function store()
    {
        return $this->hasOne(SellerStore::class);
    }

    public function nationalIdentity()
    {
        return $this->hasOne(NationalIdentity::class);
    }

}
