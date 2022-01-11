<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use Illuminate\Http\Request;

use function Psy\debug;

class AudioController extends Controller
{
    public function store(Audio $audio,Request $request)
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
        if($request->file_name && $request->size){
            $audio->file_name = $request->file_name;
            $audio->size = $request->size;
        }else if ($file = $request->file('file')) {
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

}
