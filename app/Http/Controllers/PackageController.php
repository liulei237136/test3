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
        // return Redirect::route('package.show', ['package' => $package->id, 'tab' => 'info']);

        return Redirect::back();
    }

    public function show(Package $package)
    {
        $data = $this->commonInfo($package);

        if ($data['canEdit']) {
            return Inertia::render('Package/EditBasicInfo', $data);
        } else {
            return Inertia::render('Package/ShowBasicInfo', $data);
        }

    }

    public function audio(Package $package, Request $request)
    {
        $data = $this->commonInfo($package);

        $data['commits'] = $package->commits()->latest('commits.created_at')->get(['id', 'title']);

        $commit_id = $request->input('commit');

        if ($commit_id) {
            $data['commit'] = Commit::findOrFail($commit_id);
        } else if ($data['commits']->isNotEmpty()) {
            $data['commit'] = Commit::findOrFail($data['commits']->first()['id']);
        }

        if ($data['canEdit']) {
            return Inertia::render('Package/EditAudio', $data);
        } else {
            return Inertia::render('Package/ShowAudio', $data);
        }
    }

    public function pulls(Package $package, Request $request)
    {
        $package->load('parent');

        $data = $this->commonInfo($package);

        $data['status'] = $request->query('status') ?? 'open';

        $data['pulls'] = $package->pulls()->where('status', $data['status'])->latest()->get();

        return Inertia::render('Package/ShowPulls', $data);
    }

    function clone (Package $package) {
        $child = $package->clone(auth()->user());

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
}
