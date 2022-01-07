<?php

namespace App\Http\Traits;

trait CommonInfoTrait {
    public function commonInfo($package){
        $favoritesCount = $package->favoritesCount;

        $isFavorited = auth()->user() ? $package->isFavorited() : null;

        $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

        return compact('package','favoritesCount', 'isFavorited', 'canEdit');
    }
}
