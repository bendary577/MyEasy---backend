<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CodeIndexedTrait;

class StoreProduct extends Model
{
    use HasFactory, CodeIndexedTrait;

    protected $fillable = [
        'code',
        'description',
        'available_number',
        'status',
        'ratings_number',
        'ratings_value',
    ]; 

    public function product() 
    { 
      return $this->morphOne('App\Models\Product', 'product');
    }
    
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Raiting::class);
    }
}
