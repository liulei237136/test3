<?php

namespace App\Http\Controllers;

use App\Models\Pull;
use App\Models\PullComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PullCommentController extends Controller
{
    public function store(Pull $pull, Request $request)
    {
        if (auth()->id() !== $pull->childPackage->author->id && auth()->id() !== $pull->parentPackage->author->id) {
            abort(401);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1024']
        ]);

        $validated['author_id'] = auth()->id();
        $validated['pull_id'] = $pull->id;

        PullComment::create($validated);

        return Redirect::route('pull.show', ['package' => $pull->parentPackage->id, 'pull' => $pull]);
    }
}
