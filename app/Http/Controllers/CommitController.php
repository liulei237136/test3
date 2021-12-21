<?php

namespace App\Http\Controllers;

use App\Models\Commit;
use App\Models\Package;
use Illuminate\Http\Request;

class CommitController extends Controller
{
    public function store(Package $package, Commit $commit){
        if(auth()->id() !== $package->author->id) return;
        //todo validate

        $ids = request()->input('ids');

        // $table->id();
        // $table->unsignedBigInteger('package_id');
        // $table->unsignedBigInteger('author_id');
        // $table->string('title');
        // $table->string('description');
        // $table->json('audio');
        // $table->timestamps();
        $commit->package_id = $package->id;
        $commit->author_id = auth()->id();
        $commit->title='初次保存';
        // $commit->audio=json_encode($ids);
        $commit->save();

        return $commit;
    }
}
