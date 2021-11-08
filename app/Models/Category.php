<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function store()
    {
        return $this->hasMany(Store::class);
    }
}

