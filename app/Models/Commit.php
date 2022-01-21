<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['author'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function getAudioListAttribute()
    {
        return Audio::find(json_decode($this->audio_ids));
    }
}
