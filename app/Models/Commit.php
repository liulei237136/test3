<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    use HasFactory;

    protected $casts = [
        'audio' => 'array',
        'path' => 'array',
    ];

    public function package(){
        return $this->belongsTo(Package::class);
    }
}
