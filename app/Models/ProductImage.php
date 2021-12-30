<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    public function file() 
    { 
      return $this->morphOne('App\Models\File', 'file');
    }

    public function storeProduct()
    {
        return $this->belongsTo(StoreProduct::class);
    } 
}
