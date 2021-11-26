<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['author','parent'];

    public function scopeCategory(Builder $builder, $category)
    {
        if (!is_null($category)) {
            $builder->where('category', $category);
        }

        return $builder;
    }

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

    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }


    public function audio(){
        return $this->hasMany(Audio::class);
    }

    public function parent(){
        return $this->belongsTo(Package::class, 'parent_id');
    }
}
