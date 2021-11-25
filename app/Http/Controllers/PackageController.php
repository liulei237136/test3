<?php

namespace App\Http\Controllers;

use App\Http\Resources\AudioResource;
use App\Http\Resources\PackageResource;
use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Mockery\Generator\StringManipulation\Pass\Pass;

class PackageController extends Controller
{
    public function index()
    {
        $package = PackageResource::collection(
            Package::with('author')
            ->category(request('category'))
            ->month(request('month'))
            ->search(request('term'))
            ->paginate()
        );

        $categories = DB::table('packages')
            ->selectRaw('distinct category')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->category,
                    'label' => $item->category,
                ];
            })->unique('value')->values();

        $months = DB::table('packages')
        ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label, created_at as sort')
        ->orderByDesc('sort')
        ->get();

        return Inertia::render('Package/Index', [
            'package' => $package,
            'categories' => $categories,
            'months' => $months,
            'queryParams' => request()->all(['category', 'month', 'term'])
        ]);
    }

    public function create(){
        return Inertia::render('Package/Create');
    }

    public function init(Package $package){
        return Inertia::render('Package/Init', ['package' => $package]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => Rule::in(['毛毛虫', '小达人', '卷之友']),
            'description' => ['required', 'string', 'max:3000'],
        ]);

        $validated['author_id'] = auth()->id();

        $package = Package::create($validated);

        return $package;
    }

    public function editInfo(Package $package){
        return Inertia::render('Package/Edit', ['package' => $package, 'tab' => 'EditPackageInfo']);
    }

    public function editAudio(Package $package){
        $audio = AudioResource::collection($package->audio)->toArray([]);
        return Inertia::render('Package/Edit', ['package' => ['id'=>$package->id], 'audio'=> $audio, 'tab' => 'EditPackageAudio']);
    }

    public function update(Request $request ,Package $package){
        if($package->author_id != auth()->id()) return;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => Rule::in(['毛毛虫', '小达人', '卷之友']),
            'description' => ['required', 'string', 'max:3000'],
        ]);

        $success = $package->update($validated);
        //todo error handling

        return Redirect::route('package.editInfo', ['package' => $package->id]);
    }

    public function showInfo($package){
        $modal = Package::with('author')->findOrFail($package);
        return Inertia::render('Package/Show', ['package' => $modal, 'tab' => 'ShowInfo']);
    }

    public function showAudio(Package $package){
        $audio = AudioResource::collection($package->audio)->toArray([]);
        return Inertia::render('Package/Show', ['package' => ['id'=>$package->id], 'audio'=> $audio, 'tab' => 'ShowAudio']);
    }

    public function clone(Package $package){
        //todo  该包是public的可以克隆,
        $package_id = null;
        DB::transaction(function () use($package, &$package_id) {
            // clone package
            $clonedPackageId = DB::table('packages')->insertGetId([
                'name' => '"' . $package->name . '"的克隆',
                'category' => $package->category,
                'description' => $package->description,
                'author_id' => auth()->id(),
                'cloned_from' => $package->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //clone audio of package
            $audioClonedAll = [];
            $audioCollection = DB::table('audio')
                ->where('package_id', '=', $package->id)
                ->get()
                ->toArray();
            foreach($audioCollection as $audio){
                array_push($audioClonedAll, [
                    'name' => $audio->name,
                    'file_name' => $audio->file_name,
                    'book_name' => $audio->book_name,
                    'audio_text' => $audio->audio_text,
                    'size' => $audio->size,
                    'author_id' => $audio->author_id,
                    'package_id' => $clonedPackageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('audio')->insert($audioClonedAll);

            $package_id = $clonedPackageId;

        });

        return [
            'success' => true,
            'package_id' => $package_id,
        ];

    }
}
