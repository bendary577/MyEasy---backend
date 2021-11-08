<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function sellerProfile()
    {
        return $this->belongsTo(SellerProfile::class);
    }

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function searchableAs()
    {
        return 'stores';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        $data = [
            'name' => $array['name'],
        ];
        
        return $data;
    }   
}
