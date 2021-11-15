<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Psy\debug;

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
        $file = null;
        $file_name = null;

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

    public function createFromUpload(Package $package)
    {
        debug([]);
        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $file = request()->file('file');
        debug([]);
        if(!$file) return;
        $directory = "audio/" . date('Y/m/d');
        // "audio/2021/11/12/jkdfksf"
        $file_name = $file->store($directory, 'public');

        debug([]);
        $audio = Audio::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file_name,
            'size' => $file->getSize(),
            'author_id' => auth()->id(),
        ]);

        //relation
        $package->audio()->attach($audio->id);

        debug([]);
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
            // "audio/2021/11/12/jkdfksf"
            $file_name = $file->store($directory, 'public');
            //todo can not store ?
            if(!$file_name) return;
            $audio->file_name = $file_name;
        }

        //name
        $audio->name = request()->name;

        //size
        if($file && $file_name){
            $audio->size = $file->getSize();
        }

        //audio_text
        $audio->audio_text = request()->audio_text;

        //book_name
        $audio->book_name = request()->book_name;

        return $audio->save();
    }
}
