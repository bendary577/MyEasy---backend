<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Models\Store;
use Models\CustomerProfile;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'store_id',
        'description',
        'photo_path',
        'price',
        'available_number',
        'status',
        'rating',
        'ratings_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function rating()
    {
        return $this->hasMany(Raiting::class);
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
