<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'has_store',
        'customers_number',
        'badge',
        'delivery_speed',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function activation_documents()
    {
        return $this->hasMany(File::class);
    }

}
