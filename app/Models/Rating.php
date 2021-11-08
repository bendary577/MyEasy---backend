<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'user_id',
        'product_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
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
