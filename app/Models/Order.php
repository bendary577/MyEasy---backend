<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'products',
        'status',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class);
    }

}
