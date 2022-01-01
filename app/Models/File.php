<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $with = ['file'];

    protected $fillable = [
        'name',
        'extention',
        'path',
    ];

    public function file()
    {
      return $this->morphTo();
    }

}
