<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Pull;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Http\Traits\CommonInfoTrait;
use Inertia\Inertia;

class PullController extends Controller
{
    use CommonInfoTrait;

    public function store(Request $request)
    {
        //todo validate

        info('0000000000');

        $child = Package::findOrFail($request->input('child'));

        if ($child->author->id !== auth()->id()) {
            abort(401);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:225'],
            'comment' => ['nullable', 'string', 'max:1024'],
        ]);

        $parent = $child->parent;

        if ($parent) {
            abort(403);
        }

        $hasOpenPull = DB::table('pulls')->where(['parent' => $parent->id, 'child' => $child->id, 'status' => 'open'])->exists();

        if ($hasOpenPull) {
            abort(403);
        }

        $data = array();

        $data['title'] = $validated['title'];

        $data['child'] = $child->id;

        $data['author_id'] = auth()->id();

        info(json_encode($data));
        $pull = $parent->pulls()->create($data);

        // if ($request->comment) {
        //     $pull->comments()->create([
        //         'content' => $request->comment,
        //         'author_id' => auth()->user(),
        //     ]);
        // }

        return Redirect::route('pull.show', ['package' => $parent->id, 'pull' => $pull->id]);
    }

    public function show(Package $package, Pull $pull)
    {
        $data = $this->commonInfo($package);

        $data['pull'] = $pull;

        Inertia::render('Pull/Show', $data);
    }
}
