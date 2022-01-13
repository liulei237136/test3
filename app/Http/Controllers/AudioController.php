<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function store(Audio $audio, Request $request)
    {
        //todo commit validate
        $request->validate([
            'file' => ['file', 'max:512000'],
            'file_name' => ['string', 'max:100'],
            'book_name' => ['string', 'max:200'],
            'original_text' => ['string', 'max:2000'],
        ], [
            'file.max' => '上传mp3文件不能大于512m',
            'file_name.max' => '文件名长度不能大于100',
            'book_name.max' => '书名长度不能大于100',
            'original_text' => '原文长度不能大于1000',
        ]);
        //from import
        if ($request->file_path && $request->file_size) {
            $audio->file_path = $request->file_path;
            $audio->file_size = $request->file_size;
        } else if ($file = $request->file('file')) {
            $directory = "audio/" . date('Y/m/d');
            $file_path = $file->store($directory, 'public');
            if (!$file_path) {
                return [
                    'success' => false,
                ];
            }
            $audio->file_path = $file_path;
            $audio->file_size = $file->getSize();
        }

        $audio->file_name = $request->file_name;
        $audio->book_name = $request->book_name;
        $audio->original_text = $request->original_text;
        $audio->author()->associate(auth()->user());

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
