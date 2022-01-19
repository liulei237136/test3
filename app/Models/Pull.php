<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pull extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['author', 'parentPackage', 'childPackage', 'comments'];

    public function comments()
    {
        return $this->hasMany(PullComment::class, 'pull_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parentPackage()
    {
        return $this->belongsTo(Package::class, 'parent');
    }

    public function childPackage()
    {
        return $this->belongsTo(Package::class, 'child');
    }
}
