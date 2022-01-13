<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Traits\CommonInfoTrait;
use App\Models\Audio;
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
            'title' => ['required', 'string', 'max:255'],
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
        $data = $this->commonInfo($package);

        if ($data['canEdit']) {
            return Inertia::render('Package/EditBasicInfo', $data);
        } else {
            return Inertia::render('Package/ShowBasicInfo', $data);
        }

    }

    public function audio(Package $package)
    {
        $data = $this->commonInfo($package);

        $data['commits'] = $package->commits()->latest()->get();

        $data['commit'] = $data['commits']->first();

        if ($data['canEdit']) {
            return Inertia::render('Package/EditAudio', $data);
        } else {
            return Inertia::render('Package/ShowAudio', $data);
        }
    }

    public function pulls(Package $package, Request $request)
    {
        $data = $this->commonInfo($package);

        $data['status'] = $request->query('status') ?? 'open';

        $data['pulls'] = $package->pulls()->where('status', $data['status'])->latest()->get();

        return Inertia::render('Package/ShowPulls', $data);
    }

    function clone (Package $package) {
        $child = $package->clone(auth()->user());

        return Redirect::route('package.show', ['package' => $child->id, 'tab' => 'audio']);
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
