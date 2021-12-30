<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
      'quantity'
  ]; 

    public function product() 
    { 
      return $this->morphOne('App\Models\Product', 'product');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
