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

    protected $appends = [];

    public function scopeSearch(Builder $builder, $term)
    {
        if (!is_null($term)) {
            $builder->where('name', 'LIKE', "%$term%");
        }

        return $builder;
    }

    public function scopePrivate(Builder $builder)
    {
        $builder->whereNotNull('private');
    }

    public function scopePublic(Builder $builder)
    {
        $builder->whereNull('private');
    }

    public function scopeResources(Builder $builder)
    {
        $builder->whereNull('parent_id');
    }

    public function scopeClones(Builder $builder)
    {
        $builder->whereNotNull('parent_id');
    }


    public function stars()
    {
        return $this->favorites();
    }

    public function star($userId)
    {
        $this->stars()->create(['user_id' => $userId]);

        return $this;
    }


    public function unStar($userId)
    {
        $this->stars()->delete(['user_id' => $userId]);

        return $this;
    }

    public function isStared($user_id = null)
    {
        if (is_null($user_id)) {
            return false;
        }
        return $this->isFavorited($user_id);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['q'] ?? null,  function ($query, $q) {
            $query->where('title', 'like', '%' . $q . '%');
        })->when($filters['type'] ?? null, function ($query, $type) {
            if ($type === 'public') {
                $query->whereNull('private');
            } elseif ($type === 'private') {
                $query->whereNotNull('private');
            } elseif ($type === 'clones') {
                $query->whereNotNull('parent_id');
            } elseif ($type === 'sources') {
                $query->whereNull('parent_id');
            }
        })->when($filters['sort'] ?? null, function ($query, $sort) {
            if ($sort === 'last_updated') {
                $query->latest('updated_at');
            } elseif ($sort === 'title') {
                $query->orderBy('title');
            } elseif ($sort === 'recently_starred') {
                $query->latest('favorites.created_at');
            }
            //todo sortby stars
        });
    }

    function clone($user = null)
    {
        //todo validate
        if (is_null($user)) {
            $user = auth()->user();
        }

        $child = new Package();
        $child->title = $this->title;
        $child->description = $this->description;
        $child->author()->associate($user);
        $child->parent()->associate($this);
        $child->save();

        $child->commits()->attach($this->commits);

        return $child;
    }

    public function clones()
    {
        return $this->hasMany(Package::class, 'parent_id');
    }

    public function isCloned($user_id = null)
    {
        if (is_null($user_id)) {
            return false;
        }
        $user = User::find($user_id);
        return $user->packages()->where('parent_id', $this->id)->exists();
    }


    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(Package::class, 'parent_id');
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
