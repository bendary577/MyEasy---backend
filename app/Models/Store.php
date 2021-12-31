<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CodeIndexedTrait;

class Store extends Model
{
    use HasFactory, CodeIndexedTrait;

    protected $with = ['store'];

    protected $fillable = [
        'title',
        'customers_number',
        'orders_number',
        'code'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function store()
    {
      return $this->morphTo();
    }

    public function storeProducts()
    {
        return $this->hasMany(StoreProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function logo()
    {
        return $this->hasOne(Logo::class);
    }

    public function getHasCompanyStoreAttribute()
    {
      return $this->store_type == 'App\Models\CompanyStore';
    }

    public function getHasSellerStoreAttribute()
    {
      return $this->store_type == 'App\Models\SellerStore';
    }

    public function searchableAs()
    {
        return 'stores';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        $data = [
            'name' => $array['name'],
        ];
        
        return $data;
    }   
}
