<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Traits\CommonInfoTrait;
use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PackageController extends Controller
{
    use CommonInfoTrait;

    public function index()
    {
        $package = PackageResource::collection(
            Package::with('author')
                ->latest()
                ->paginate(15)
        );

        return Inertia::render('Package/Index', [
            'package' => $package,
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
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'max:3000', 'min:3'],
            'private' => Rule::in(["0", "1"]),
        ], [
            'title.min' => '名称最少3个字符长',
            'description.min' => '描述最少3个字符长',
            'private' => '是否私有只能是或否',
        ]);

        $validated['author_id'] = auth()->id();

        $package = Package::create($validated);

        return Redirect::route('package.init', ['package' => $package->id]);
    }

    public function update(Request $request, Package $package)
    {
        if ($package->author_id != auth()->id()) {
            abort(401);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'max:3000', 'min:3'],
            'private' => Rule::in(["0", "1"]),
        ], [
            'title.min' => '名称最少3个字符长',
            'description.min' => '描述最少3个字符长',
            'private' => '是否私有只能是或否',
        ]);

        $package->update($validated);

        return Redirect::back();
    }

    public function show(Package $package)
    {
        $this->appendAttribute($package);

        if ($package['canEdit']) {
            return Inertia::render('Package/EditBasicInfo', compact('package'));
        } else {
            return Inertia::render('Package/ShowBasicInfo', compact('package'));
        }
    }

    public function audio(Package $package, Request $request)
    {
        $this->appendAttribute($package);

        $package->commits = $package->commits()->latest('commits.created_at')->get(['id', 'title']);

        $commit_id = $request->input('commit');

        if ($commit_id) {
            $package->commit = Commit::findOrFail($commit_id);
        } else if ($package->commits->isNotEmpty()) {
            $package->commit = Commit::findOrFail($package->commits->first()['id']);
        }

        if ($package->canEdit) {
            return Inertia::render('Package/EditAudio', compact('package'));
        } else {
            return Inertia::render('Package/ShowAudio', compact('package'));
        }
    }

    public function pulls(Package $package, Request $request)
    {
        $package->load('parent');

        $data = $this->commonInfo($package);

        $data['status'] = $request->query('status') ?? 'open';

        $data['openPullCount'] = $package->pulls()->where('status', 'open')->count();

        $data['closePullCount'] = $package->pulls()->count() - $data['openPullCount']; //close include close and merge

        $data['pulls'] = $package->pulls()->where('status', $data['status'])->latest()->get();

        return Inertia::render('Package/ShowPulls', $data);
    }

    function clone(Package $package)
    {
        $child = $package->clone();

        return Redirect::route('package.show', ['package' => $child]);
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
    // public function commonInfo($package)
    // {
    //     $favoritesCount = $package->favoritesCount;

    //     $isFavorited = auth()->user() ? $package->isFavorited() : null;

    //     $canEdit = auth()->user() && auth()->user()->id === $package->author->id;

    //     $package->loadCount('children');

    //     return compact('package', 'favoritesCount', 'isFavorited', 'canEdit');
    // }
    protected function appendAttribute($package)
    {
        $package->loadCount('stars');
        $package->isStared = $package->isFavorited();
        $package->loadCount('clones');
        $package->isCloned = $package->isCloned(auth()->id());
        $package->canEdit = auth()->check() && (int)auth()->id() === (int)$package->author->id;


        return $package;
    }
}
