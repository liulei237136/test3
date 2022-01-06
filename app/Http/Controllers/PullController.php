<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PullController extends Controller
{
    //
    public function index(Package $package,Request $request){
        $commits = $package->commits()->latest()->get();


        $package->loadCount('children');

        $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

        $favoritesCount = $package->favoritesCount;

        $isFavorited = auth()->user() ? $package->isFavorited() : null;

        $tab = 'pull';

        $status = $request->input('status') ?? 'open' ;

        $pulls = $package->pulls()->where('status', $status)->get();

        $data = compact('package', 'canEdit', 'favoritesCount', 'isFavorited', 'tab', 'commits', 'pull');

        return Inertia::render('Package/Show');
    }
}
