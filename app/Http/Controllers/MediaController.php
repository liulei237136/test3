<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        $media = MediaResource::collection(
            Media::with('author')
            ->type(request('fileType'))
            ->month(request('month'))
            ->paginate()
        );

        $fileTypes = Media::selectRaw('distinct mime_type')
        ->get()
        ->map(function ($item) {
            return [
                'value' => $item->file_type,
                'label' => ucfirst($item->file_type)
            ];
        })->unique('value')->values();

        $months = DB::table('media')
        ->selectRaw('distinct DATE_FORMAT(created_at, "%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
        ->orderByDesc('value')
        ->get();


        return Inertia::render('Media/Index', [
            'media' => $media,
            'fileTypes' => $fileTypes,
            'months' => $months,
            'queryParams' => request()->all(['fileType', 'month'])
        ]);
    }

    public function store()
    {

        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $file = request()->file('file');

        $media = Media::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'author_id' => auth()->id()
        ]);

        $directory = "media/{$media->created_at->format('Y/m/d')}/{$media->id}";
        $file->storeAs($directory, $media->file_name, 'public');

        return [
            'id' => $media->id,
            'preview_url' => $media->preview_url
        ];
    }
}
