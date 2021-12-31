<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;

    protected $with = ['product'];

    protected $fillable = [
        'name',
        'price',
    ]; 

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function product()
    {
      return $this->morphTo();
    }

    public function getHasStoreProductAttribute()
    {
      return $this->product_type == 'App\Models\StoreProduct';
    }

    public function getHasOrderProductAttribute()
    {
      return $this->product_type == 'App\Models\OrderProduct';
    }

    public function getHasInvoiceItemAttribute()
    {
      return $this->product_type == 'App\Models\InvoiceItem';
    }

    public function searchableAs()
    {
        return 'products';
    }

    /*
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $data = [
            'name' => $array['name'],
            'description' => $array['description'],
        ];
        
        return $data;
    }*/
}
