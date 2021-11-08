<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id'
    ];

    public function sellerProfile(){
        return $this->belongsTo(SellerProfile::class);
    }

    public function companyProfile(){
        return $this->belongsTo(CompanyProfile::class);
    }

    public function customerProfile(){
        return $this->belongsTo(CustomerProfile::class);
    }
}
