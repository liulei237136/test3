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

    public function store(Package $package, Audio $audio)
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
            if(!$file_name) return;
            $audio->file_name = $file_name;
            $audio->size = $file->getSize();
        }

        request()->name && $audio->name = request()->name;
        request()->book_name && $audio->book_name = request()->book_name;
        request()->audio_text && $audio->audio_text = request()->audio_text;
        $audio->author_id= auth()->id();

        $result = $audio->save();

        if($result){
            $package->audio()->attach($audio->id);

            return [
                'type' => 'insert',
                'success' => true,
                'data' =>  $audio,
                'uuid' => request()->uuid,
            ];
        }
        return [
            'type' => 'insert',
            'success' => false,
            'xid' => request()->uuid,
        ];


    }

    public function createFromUpload(Package $package)
    {
        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $file = request()->file('file');
        if(!$file) return;
        $directory = "audio/" . date('Y/m/d');
        // "audio/2021/11/12/jkdfksf"
        $file_name = $file->store($directory, 'public');

        $audio = Audio::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file_name,
            'size' => $file->getSize(),
            'author_id' => auth()->id(),
        ]);

        //relation
        $package->audio()->attach($audio->id);

        return $audio;
    }

    public function destroy(Package $package, Audio $audio){
        //todo  check if the current user is the author of the audio

        $result = $audio->delete();
        if($result){
            return [
                'type' => 'delete',
                'success' => true,
                'data' => $audio,
            ];
        }
        return [
            'type' => 'delete',
            'success' => false,
        ];
    }

    public function update(Package $package, Audio $audio){
        //todo  check if the current user is the author of the audio

        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        //file_name && size
        $file = request()->file('file');
        if($file){
            $directory = "audio/" . date('Y/m/d');
            // "audio/2021/11/12/jkdfksf"
            $file_name = $file->store($directory, 'public');
            //todo can not store ?
            if(!$file_name){
                return [
                    'type' => 'update',
                    'success' => false,
                ];
            }
            $audio->file_name = $file_name;
            $audio->size = $file->getSize();
        }

        $audio->name = request()->name;
        $audio->audio_text = request()->audio_text;
        $audio->book_name = request()->book_name;

        $result = $audio->save();
        if($result){
            return [
                'type' => 'update',
                'success' => true,
                'data' => $audio,
                'uuid' => request()->uuid,
            ];
        }
        return [
            'type' => 'update',
            'success' => false,
            'uuid' => request()->uuid,
        ];
    }
}
