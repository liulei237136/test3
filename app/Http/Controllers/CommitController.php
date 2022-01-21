<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Http\Traits\CommonInfoTrait;


class CommitController extends Controller
{
    use CommonInfoTrait;

    public function index(Package $package, Request $request)
    {

        $data = $this->commonInfo($package);

        // $data['commits'] = $package->commits;
        $author_id = $request->input('author');

        if ($author_id) {
            $data['package']->load(['commits' => function ($query) use ($author_id) {
                $query->where('commits.author_id', $author_id);
            }]);
        } else {
            $data['package']->load('commits');
        }

        info(json_encode($data));
        return Inertia::render('Commit/CommitIndex', $data);
    }

    public function store(Request $request)
    {
        $package = Package::findOrFail($request->input('package'));

        if (auth()->id() !== $package->author->id) {
            abort(401);
        }

        $validated = $request->validate(
            [
                'title' => ['required', 'string', 'min:3', 'max:256'],
                'description' => ['nullable', 'string'],
                'audio_ids' => ['required'],
            ],
            [
                'title.min' => '保存名最短3个字符',
                'title.max' => '保存名最长256个字符',
            ]
        );

        $validated['author_id'] = auth()->id();

        $commit = $package->commits()->create($validated);

        return Redirect::route('package.audio', ['package' => $package->id, 'commit' => $commit->id])->with('success', '保存成功');
    }

    public function audio(Commit $commit)
    {
        //todo the package is not private or the use is the author of commit
        // $audio = Audio::find(json_decode($commit->audio));
        $audio_list = $commit->audioList;

        return compact('audio_list');
    }
}
