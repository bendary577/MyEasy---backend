<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'status',
        'has_reply',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attachments(){
        return $this->hasMany(ComplaintAttachment::class);
    }
}
