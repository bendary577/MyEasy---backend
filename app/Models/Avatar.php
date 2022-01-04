<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasFactory;

    protected $with = ['file'];

    protected $appends = ['avatar_base64_string'];

    public function file() 
    { 
      return $this->morphOne('App\Models\File', 'file');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    } 

    public function getAvatarBase64StringAttribute()
    {
      $type = pathinfo($this->file->path, PATHINFO_EXTENSION);
      $data = file_get_contents($this->file->path);
      $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
      return $base64;
    }
    
}
