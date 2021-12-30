<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $with = ['store'];

    protected $fillable = [
        'title',
        'customers_number',
        'orders_number',
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
