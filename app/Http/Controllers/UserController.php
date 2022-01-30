<?php

namespace App\Http\Controllers;

use App\Filters\UserPackagesFilter;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $targetUser;

    public function show(User $user, Request $request)
    {
        $request->validate(
            [
                'tab' => ['nullable', 'string', Rule::in(['packages', 'stars'])]
            ]
        );

        $this->targetUser = $user;

        if (is_null($request->tab)) {
            $tab = 'overview';
        } else {
            $tab = $request->tab;
        }

        $data = $this->{$tab}();

        $data['targetUser'] = $user;

        $template = 'User/' . ucfirst($tab);

        return Inertia::render($template, $data);
    }

    public function packages()
    {
        $filters = request()->all('q', 'type', 'sort');

        $queryBuilder = $this->targetUser->packages();

        // dd(get_class($queryBuilder));

        if (!auth()->check() || auth()->id() !== $this->targetUser->id) {
            $queryBuilder->whereNull('private');

            if ($filters['type'] === 'public' || $filters['type'] === 'private') {
                unset($filters['type']);
            }
        }

        if (empty($filters['sort'])) {
            $filters['sort'] = 'last_updated';
        }

        return  [
            'filters' => request()->all('q', 'type', 'sort'),
            'packages' => $queryBuilder
                ->filter($filters)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($package) => [
                    'id' => $package->id,
                    'title' => $package->title,
                    'updated_at' => $package->updated_at->toDatetimeString(),
                ])
        ];
    }

    public function stars()
    {
        $filters = request()->all('q', 'type', 'sort');

        $queryBuilder = $this->targetUser->favorites()->where('favoriteable_type', Package::class)->with('favoriteable');
        // $queryBuilder = $this->targetUser->favorites()
        dd($queryBuilder->get()->toArray());

        if (!auth()->check() || auth()->id() !== $this->targetUser->id) {
            $queryBuilder->whereNull('favoriteable.private');

            if ($filters['type'] === 'public' || $filters['type'] === 'private') {
                unset($filters['type']);
            }
        }

        if (empty($filters['sort'])) {
            $filters['sort'] = 'recently_starred';
        }

        return  [
            'filters' => request()->all('q', 'type', 'sort'),
            'packages' => $queryBuilder
                // ->filter($filters)
                // ->filter($filters)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($item) => [
                    'id' => $item->favoriteable->id,
                    'title' => $item->favoriteable->title,
                    'updated_at' => $item->favoriteable->updated_at->toDatetimeString(),
                ])
        ];
    }

    public function overview()
    {
        return [];
    }
}
