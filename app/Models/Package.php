<?php

namespace App\Models;

use Carbon\Carbon;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    use Favoriteable;

    protected $guarded = [];

    protected $with = ['author', 'parent'];

    public function scopeMonth(Builder $builder, $date)
    {
        if (!is_null($date)) {
            $builder->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $date)->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)->endOfMonth(),
            ]);
        }

        return $builder;
    }

    public function scopeSearch(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('name', 'LIKE', "%$term%");
        }

        return $builder;
    }

    function clone () {
        //todo validate

        $child = new Package();
        $child->title = $this->title;
        $child->description = $this->description;
        $child->author()->associate(auth()->user());
        $child->parent()->associate($this);
        $child->save();

        $child->commits()->attach($this->commits);

        return $child;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(Package::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Package::class, 'parent_id');
    }

    public function commits()
    {
        // return $this->belongsToMany(Commit::class, 'package_commit')->withTimestamps();
        return $this->belongsToMany(Commit::class, 'package_commit');
    }

    public function pulls()
    {
        return $this->hasMany(Pull::class, 'parent');
    }

}
