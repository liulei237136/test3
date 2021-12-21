<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommitController extends Controller
{
    public function store(Package $package, Commit $commit)
    {
        if (auth()->id() !== $package->author->id) return;
        //todo validate

        $ids = request()->input('ids');
        $title = request()->input('title');

        // $table->id();
        // $table->unsignedBigInteger('package_id');
        // $table->unsignedBigInteger('author_id');
        // $table->string('title');
        // $table->string('description');
        // $table->json('audio');
        // $table->timestamps();
        $commit->package_id = $package->id;
        $commit->author_id = auth()->id();
        $commit->title = $title;
        $commit->audio = json_encode($ids);
        if ($commit->save()) {
            return $commit;
        }
        return [];
    }

    public function show(Package $package, Commit $commit)
    {
        // $commit = request()->input('commit');

        // $package->loadCount('children');

        // $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

        // $favoritesCount = $package->favoritesCount;

        // $isFavorited = auth()->user() ? $package->isFavorited() : null;

        // return Inertia::render('Package/Show', compact('package', 'canEdit', 'favoritesCount', 'isFavorited', 'commit'));

        //todo the package is not private or the use is the author of commit
        info($commit->audio);
        $audio = Audio::find(json_decode($commit->audio));
        info($audio);

    // $commit = request()->input('commit');

        $package->loadCount('children');

        $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

        $favoritesCount = $package->favoritesCount;

        $isFavorited = auth()->user() ? $package->isFavorited() : null;

        return Inertia::render('Package/Show', compact('package', 'canEdit', 'favoritesCount', 'isFavorited', 'commit'));
    }

    public function index(Package $package)
    {
        //todo
        $commits = $package->commits;

        return compact('commits');
    }
}
