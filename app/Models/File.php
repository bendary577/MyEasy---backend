<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'extention',
        'path',
        'size',
    ];

    
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    } 

    public function company()
    {
        return $this->belongsTo(Company::class);
    } 

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    } 

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    } 
}
