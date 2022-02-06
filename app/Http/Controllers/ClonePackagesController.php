<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ClonePackagesController extends Controller
{
    function store(Package $package)
    {
        // if(!auth()->user()->hasCloned($package)){
        if(!$package->clonedBy(auth()->user())){
            $child = $package->clone();

            return Redirect::route('package.show', ['package' => $child]);
        }
    }
}
