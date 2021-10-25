<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function package()
    {
        return $this->belongsToMany(Package::class, 'package_audio');
    }

    public function getUrlAttribute()
    {
        return url("audio/{$this->created_at->format('Y/m/d')}/{$this->id}/{$this->file_name}");
    }
}
