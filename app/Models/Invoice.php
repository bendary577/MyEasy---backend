<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $with = ['invoice'];

    protected $fillable = [
        'code',
        'url',
        'customer_name',
        'total_price',
        'paid',
        'paid_at',
        'owner_type',
        'currency',
        'expiration_date',
        'number_of_items',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function invoice()
    {
      return $this->morphTo();
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function generateCode()
    {
        return rand(pow(10, 8-1), pow(10, 8)-1);
    }

}