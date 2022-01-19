<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonInfoTrait;
use App\Models\Package;
use App\Models\Pull;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PullController extends Controller
{
    use CommonInfoTrait;

    public function store(Request $request)
    {
        //todo validate
        $child = Package::findOrFail($request->input('child'));

        if ($child->author->id !== auth()->id()) {
            abort(401);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:225'],
            'comment' => ['nullable', 'string', 'max:1024'],
        ]);

        if (!$child->parent) {
            abort(403);
        }

        $hasOpenPull = DB::table('pulls')->where(['parent' => $child->parent->id, 'child' => $child->id, 'status' => 'open'])->exists();

        if ($hasOpenPull) {
            abort(403);
        }

        $data = array();

        $data['title'] = $validated['title'];

        $data['child'] = $child->id;

        $data['author_id'] = auth()->id();

        $pull = $child->parent->pulls()->create($data);

        if ($request->comment) {
            $pull->comments()->create([
                'content' => $request->comment,
                'author_id' => auth()->id(),
            ]);
        }

        return Redirect::route('pull.show', ['package' => $child->parent->id, 'pull' => $pull->id]);
    }

    public function close(Pull $pull)
    {
        //todo validate
        if (auth()->id() !== $pull->childPackage->author->id && auth()->id() !== $pull->parentPackage->author->id) {
            abort(401);
        }

        if ($pull->status !== 'open') {
            abort(403);
        }

        $pull->status = 'closed';

        $pull->save();

        return Redirect::route('pull.show', ['package' => $pull->parentPackage->id, 'pull' => $pull]);
    }

    public function open(Pull $pull)
    {
        //todo validate
        if (auth()->id() !== $pull->childPackage->author->id && auth()->id() !== $pull->parentPackage->author->id) {
            abort(401);
        }

        if ($pull->status !== 'closed') {
            abort(403);
        }

        $pull->status = 'open';

        $pull->save();

        return Redirect::route('pull.show', ['package' => $pull->parentPackage->id, 'pull' => $pull]);
    }

    public function show(Package $package, Pull $pull)
    {
        $data = $this->commonInfo($package);

        $data['pull'] = $pull;

        Inertia::render('Pull/Show', $data);
    }
}
