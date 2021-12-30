<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyStore extends Model
{
    use HasFactory;

    public function store() 
    { 
      return $this->morphOne('App\Models\Store', 'store');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
