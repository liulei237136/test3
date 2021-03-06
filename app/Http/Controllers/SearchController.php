<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    // public function index()
    // {
    //     $media = MediaResource::collection(
    //         Media::with('author')
    //         ->type(request('fileType'))
    //         ->month(request('month'))
    //         ->paginate()
    //     );

    //     $fileTypes = Media::selectRaw('distinct mime_type')
    //     ->get()
    //     ->map(function ($item) {
    //         return [
    //             'value' => $item->file_type,
    //             'label' => ucfirst($item->file_type)
    //         ];
    //     })->unique('value')->values();

    //     $months = DB::table('media')
    //     ->selectRaw('distinct DATE_FORMAT(created_at, "%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
    //     ->orderByDesc('value')
    //     ->get();

    //     return Inertia::render('Media/Index', [
    //         'media' => $media,
    //         'fileTypes' => $fileTypes,
    //         'months' => $months,
    //         'queryParams' => request()->all(['fileType', 'month'])
    //     ]);
    // }
    public function index()
    {
        //todo 区分private 还有自己的项目

        //todo 验证参数

        //q=term, sort=sort, order=order(desc, asc)
        $queryParams = request()->all(['q', 'sort', 'order']);
        $q = $queryParams['q'];
        $sort = $queryParams['sort'];
        $order = $queryParams['order'];

        if (!$q) {
            return Inertia::render('Search', ['queryParams' => $queryParams]);
        }

        if (!$sort) {
            $sort = $queryParams['sort'] = 'match';
            $order = $queryParams['order'] = 'desc';
        }
        //todo
        $package = Package::query()->where('title', 'like', '%' . $q . '%');
        switch ($sort) {
            case 'match':
                //todo match is the highest points not latest()
                $package->latest();
                break;
            //todo
            case 'stars':
                $package->has('star')
                    ->withCount('star')
                    ->orderBy('star_count', $order);
            case 'clones':
                $package->has('clone')
                    ->withCount('star')
                    ->orderBy('clone_count', $order);
            case 'updated':
                //todo update a audio should touch a package
                $package->latest('updated_at');
        }
        $package = $package->paginate(6);

        return Inertia::render('Search', ['package' => $package, 'queryParams' => $queryParams]);
    }
}
