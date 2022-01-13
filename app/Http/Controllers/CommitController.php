<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Illuminate\Http\Request;

class CommitController extends Controller
{

    public function store(Commit $commit, Request $request)
    {
        //todo validate
        $package = Package::findOrFail($request->input('package'));

        if (auth()->id() !== $package->author->id) {
            abort(401);
        }

        $commit->author_id = auth()->id();
        $commit->title = $request->input('title');
        $commit->description = $request->input('description');
        $commit->audio_ids = $request->input('audio_ids');
        if ($commit->save()) {
            $package->commits()->attach($commit);
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
