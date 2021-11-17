<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['url'];

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
        if($this->file_name){
            return url("storage/{$this->file_name}");
        }else{
            return;
        }
    }
}
