<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommitController extends Controller
{

    public function store(Package $package, Commit $commit, Request $request)
    {
        if (auth()->id() !== $package->author->id) return;
        //todo validate

        $commit->package_id = $package->id;
        $commit->author_id = auth()->id();
        $commit->title = $request->input('title');
        $commit->description = $request->input('description');
        $commit->audio = json_encode($request->input('ids'));
        $commit->path = json_encode($request->input('path'));
        if ($commit->save()) {
            $package->commits()->attach($commit->id);
            return [
                'success' => true,
                'data' => $commit,
            ];
        }
        return ['success' => false];
    }

    public function audio(Commit $commit)
    {
        //todo the package is not private or the use is the author of commit
        $audio = Audio::find(json_decode($commit->audio));

        return compact('audio');
    }
}
