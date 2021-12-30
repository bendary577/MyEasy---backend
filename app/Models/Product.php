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
