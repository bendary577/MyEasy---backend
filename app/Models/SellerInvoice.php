<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerInvoice extends Model
{
    use HasFactory;

    public function invoice() 
    { 
      return $this->morphOne('App\Models\Invoice', 'invoice');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
