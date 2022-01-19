<?php

namespace App\Http\Controllers;

use App\Models\Package;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

// use GuzzleHttp\Psr7\Request;

class PullController extends Controller
{
    public function store(Request $request)
    {
        //todo validate
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:225'],
            'comment' => ['nullable', 'string', 'max:2048'],
        ]);

        $child = Package::findOrFail($request->input('child'));

        $parent = $child->parent;

        if (isEmpty($parent)) {
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

        $pull = $parent->pulls()->create($data);

        if ($request->comment) {
            $pull->comments()->create([
                'content' => $request->comment,
                'author_id' => auth()->user(),
            ]);
        }

        return Redirect::route('pull.show', ['pull' => $pull, 'package' => $parent]);
    }
}
