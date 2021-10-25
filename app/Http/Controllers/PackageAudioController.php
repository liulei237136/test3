<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageAudioController extends Controller
{
    public function edit(Package $package){
        return Inertia::render('Package/Audio/Edit', ['package' => $package]);
    }

    public function store(Package $package)
    {
        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $file = request()->file('file');

        $audio = Audio::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            // 'package' => $package->id,
            'author_id' => auth()->id()
        ]);
        // dd($audio->toArray());
        $package->audio()->attach($audio->id);

        $directory = "audio/{$audio->created_at->format('Y/m/d')}/{$audio->id}";
        $file->storeAs($directory, $audio->file_name, 'public');

        return [
            'id' => $audio->id,
            // 'preview_url' => $audio->preview_url
        ];
    }
}
