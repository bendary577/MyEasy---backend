<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Models\SellerProfile;
use Models\CompanyProfile;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'user_id',
        'expiration',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function sellerProfile()
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }

}