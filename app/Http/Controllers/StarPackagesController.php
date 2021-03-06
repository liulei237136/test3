<?php

namespace App\Http\Controllers;

use App\Models\Package;

class StarPackagesController extends Controller
{
    public function store(Package $package)
    {
        if ($package->private && $package->author->id !== auth()->id()) {
            return;
        }

        $package->star(auth()->id());
    }

    public function destroy(Package $package)
    {
        if ($package->private && $package->author->id !== auth()->id()) {
            return;
        }
        $package->unStar(auth()->id());
    }
}
