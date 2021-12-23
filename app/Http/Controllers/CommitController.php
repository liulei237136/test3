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
        if ($commit->save()) {
            return [
                'success' => true,
                'data' => $commit,
            ];
        }
        return ['success' => false];
    }

    public function audio(Package $package, Commit $commit)
    {
        //todo the package is not private or the use is the author of commit
        $audio = Audio::find(json_decode($commit->audio));
        // $audio = Audio::whereIn('id', json_decode($commit->audio))->get();

        return compact('audio');
    }
}
