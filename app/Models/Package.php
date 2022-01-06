<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use ChristianKuri\LaravelFavorite\Traits\Favoriteable;

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

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }


    public function audio()
    {
        return $this->hasMany(Audio::class);
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
        return $this->belongsToMany(Commit::class,'package_commit')->withTimestamps();
    }

    public function pulls(){
        return $this->hasMany(Pull::class, 'target_package_id');
    }
}
