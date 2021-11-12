<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageAudioController extends Controller
{
    public function edit($package){
        return Inertia::render('Package/Audio/Edit', ['package' => Package::with('audio')->findOrFail($package)]);
    }

    public function store(Package $package)

    {

        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $file = request()->file('file');
        if($file){
            $directory = "audio/" . date('Y/m/d');
            // "audio/2021/11/12/jkdfksf.mp3"
            $file_name = $file->store($directory, 'public');
        }

        $data = [];
        //name
        if(request()->name){
            $data['name'] = request()->name;
        }else if($file){
            $data['name'] = $file->getClientOriginalName();
        }

        //file_name
        if($file_name){
            $data['file_name'] = $file_name;
        }

        //size
        if($file && $file_name){
            $data['size'] = $file->getSize();
        }

        //author_id
        $data['author_id'] = auth()->id();

        $audio = Audio::create($data);

        //relation
        $package->audio()->attach($audio->id);


        return $audio;
    }

    public function destroy(Package $package, Audio $audio){
        //todo  check if the current user is the author of the audio

        return $audio->delete();
    }

    public function update(Package $package, Audio $audio){
        //todo  check if the current user is the author of the audio

        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        //file_name
        $file = request()->file('file');
        if($file){
            $directory = "audio/" . date('Y/m/d');
            // "audio/2021/11/12/jkdfksf.mp3"
            $file_name = $file->store($directory, 'public');
            if($file_name){
                $audio->file_name = $file_name;
            }
        }

        //name
        $audio->name = request()->name;

        //size
        if($file && $file_name){
            $audio->size = $file->getSize();
        }

        //author_id
        $audio->author_id = auth()->id();

        return $audio->save();
    }
}
