<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasFactory;

    public function file() 
    { 
      return $this->morphOne('App\Models\File', 'file');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    } 
}
