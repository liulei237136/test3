<?php

function create($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->make($attributes);
}

function appendAttribute($package)
{
    $package->loadCount('stars');
    $package->isStared = $package->isFavorited();
    $package->loadCount('clones');
    $package->isCloned = $package->isCloned(auth()->id());
    $package->canEdit = auth()->check() && (int)auth()->id() === (int)$package->author->id;


    return $package;
}
