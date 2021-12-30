<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInvoice extends Model
{
    use HasFactory;

    public function invoice() 
    { 
      return $this->morphOne('App\Models\Invoice', 'invoice');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
