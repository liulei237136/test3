<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Audio;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $package = PackageResource::collection(
            Package::with('author')
                ->month(request('month'))
                ->search(request('term'))
                ->paginate()
        );

        $months = DB::table('packages')
            ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label, created_at as sort')
            ->orderByDesc('sort')
            ->get();

        return Inertia::render('Package/Index', [
            'package' => $package,
            'months' => $months,
            'queryParams' => request()->all(['month', 'term']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Package/Create');
    }

    public function init(Package $package)
    {
        return Inertia::render('Package/Init', ['package' => $package]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'private' => Rule::in([true, false]),
        ]);

        $validated['author_id'] = auth()->id();

        $package = Package::create($validated);

        return Redirect::route('package.init', ['package' => $package->id]);
    }

    public function update(Request $request, Package $package)
    {
        if ($package->author_id != auth()->id()) {
            return;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
        ]);

        $success = $package->update($validated);
        //todo error handling

        return Redirect::route('package.show', ['package' => $package->id, 'tab' => 'info']);
    }

    public function show(Package $package)
    {

        // $commitId = request()->query('commit');

        // $commits = $package->commits()->latest()->get();

        // $commit = null;
        // if ($commitId){
        //     $commit = $commits->find($commitId);
        //     if(!$commit) throw new Exception('error commit', 404);
        // }else if($commits->isNotEmpty()){
        //     $commit = $commits->first();
        // }

        // $package->loadCount('children');

        // $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

        // $favoritesCount = $package->favoritesCount;

        // $isFavorited = auth()->user() ? $package->isFavorited() : null;

        // $tab = request()->query('tab') ?? 'info';

        // $data = compact('package', 'canEdit', 'favoritesCount', 'isFavorited', 'tab', 'commits');

        // if ($commit) {
        //     $data['commit'] = $commit;
        // }

        // return Inertia::render('Package/Show', $data);
        $favoritesCount = $package->favoritesCount;

        $isFavorited = auth()->user() ? $package->isFavorited() : null;

        $data = compact('package', 'favoritesCount', 'isFavorited');

        $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

        if ($canEdit) {
            return Inertia::render('Package/EditBasicInfo', $data);
        } else {
            return Inertia::render('Package/ShowBasicInfo', $data);
        }

    }

    public function audio(Package $package)
    {
        return Audio::toBase()->where('package_id', $package->id)->get();
        return [['name' => 'file1'], ['name' => 'file2'], ['name' => 'file3']];
    }

    function clone (Package $package) {
        $clonedPackageId = null;

        DB::transaction(function () use ($package, &$clonedPackageId) {
            $newPackage = new Package();
            $newPackage->name = $package->name;
            $newPackage->description = $package->description;
            $newPackage->author()->associate(auth()->user());
            $newPackage->parent()->associate($package);
            $newPackage->save();

            $newPackage->commits()->attach($package->commits);

            $clonedPackageId = $newPackage->id;
        });

        return Redirect::route('package.show', ['package' => $clonedPackageId, 'tab' => 'audio']);
    }

    public function toggleFavorite(Package $package)
    {
        $package->toggleFavorite();

        info('.....' . url()->previous());

        //是未登录用户，没有previous url
        if (!url()->previous()) {
            return Redirect::back();
        }

    }

    public function toggleFavoriteAfterLogin(Package $package)
    {
        $package->toggleFavorite();
        return Redirect::back();
    }
}
