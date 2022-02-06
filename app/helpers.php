<?php
function appendAttribute($package)
{
    $package->loadCount('stars');
    $package->isStared = $package->isFavorited();
    $package->loadCount('clones');
    if (auth()->check()) {
        $package->hasCloned = $package->clonedBy(auth()->id());
        $package->hasClonedPackage = auth()->user()->packages()->where('parent_id', $package->id)->first();
        $package->canEdit = auth()->check() && (int)auth()->id() === (int)$package->author->id;
    }


    return $package;
}
