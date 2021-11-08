<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'price',
        'total',
        'state',
        'user_id',
        'product_id'
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
