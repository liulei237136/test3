<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Psy\debug;

class PackageAudioController extends Controller
{

    public function edit($package)
    {
        //todo 只有作者能编辑包
        return Inertia::render('Package/Audio/Edit', ['package' => Package::with('audio')->findOrFail($package)]);
    }

    public function store(Package $package, Audio $audio,Request $request)
    {

        $request->validate([
            'file' => ['file', 'max:512000'],
            'name' => ['string', 'max:100'],
            'book_name' => ['string', 'max:200'],
            'audio_text' => ['string', 'max:2000'],
        ], [
            'max' => '上传的音频文件不能大于512m',
            'name' => '文件名不能长于50个字',
            'book_name' => '所属书名不能长于100个字',
            'audio_text' => '音频的文字内容不能长于1000个字',
        ]);

        if ($file = $request->input('file')) {
            $directory = "audio/" . date('Y/m/d');
            $file_name = $file->store($directory, 'public');
            if (!$file_name) {
                return [
                    'success' => false,
                ];
            }
            $audio->file_name = $file_name;
            $audio->size = $file->getSize();
        }

        $audio->name = $request->name;
        $audio->book_name = $request->book_name;
        $audio->audio_text =$request->audio_text;
        $audio->author_id = auth()->id();
        $audio->package_id = $package->id;

        if ($audio->save()) {
            return [
                'success' => true,
                'data' => $audio,
            ];
        }

        return [
            'success' => false,
        ];
    }

    public function initUpload(Package $package)
    {
        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => '上传的音频文件不能大于512m.'
        ]);

        $file = request()->file('file');
        if (!$file) return;
        $directory = "audio/" . date('Y/m/d');
        // "audio/2021/11/12/jkdfksf"
        $file_name = $file->store($directory, 'public');

        $audio = Audio::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file_name,
            'size' => $file->getSize(),
            'author_id' => auth()->id(),
            'package_id' => $package->id,
        ]);

        return $audio;
    }

    public function destroy(Package $package, Audio $audio)
    {
        if ($package->author->id != auth()->id()) return;

        $result = $audio->delete();
        if ($result) {
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

    public function update(Package $package, Audio $audio)
    {
        if ($package->author->id != auth()->id()) return;

        request()->validate([
            'file' => ['file', 'max:512000'],
            'name' => ['string', 'max:100'],
            'book_name' => ['string', 'max:200'],
            'audio_text' => ['string', 'max:2000'],
            'uuid' => ['string', 'max:100'],
        ], [
            'max' => '上传的音频文件不能大于512m',
            'name' => '文件名不能长于50个字',
            'book_name' => '所属书名不能长于100个字',
            'audio_text' => '音频的文字内容不能长于1000个字',
            'uuid' => 'xxx',
        ]);

        //file_name && size
        $file = request()->file('file');
        if ($file) {
            $directory = "audio/" . date('Y/m/d');
            // "audio/2021/11/12/jkdfksf"
            $file_name = $file->store($directory, 'public');
            //todo can not store ?
            if (!$file_name) {
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
        if ($result) {
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
