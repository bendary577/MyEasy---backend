<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    use HasFactory;

    public function file() 
    { 
      return $this->morphOne('App\Models\File', 'file');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
