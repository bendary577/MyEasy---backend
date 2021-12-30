<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'has_store',
        'badge',
        'delivery_speed',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function invoices()
    {
        return $this->hasMany(CompanyInvoice::class);
    }

    public function store()
    {
        return $this->hasOne(CompanyStore::class);
    }

    public function taxCard()
    {
        return $this->hasOne(TaxCard::class);
    }

    public function commercialRecord()
    {
        return $this->hasOne(CommercialRecord::class);
    }

}
