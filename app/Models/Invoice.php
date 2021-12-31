<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CodeIndexedTrait;

class Invoice extends Model
{
    use HasFactory, CodeIndexedTrait;

    protected $with = ['invoice'];

    protected $fillable = [
        'code',
        'customer_name',
        'total_price',
        'paid',
        'paid_at',
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

}