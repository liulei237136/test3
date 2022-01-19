<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PullComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['author'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function pull()
    {
        return $this->belongsTo(Pull::class, 'pull_id');
    }
}
