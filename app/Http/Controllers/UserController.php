<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{

    public function show(User $user, Request $request)
    {
        //todo validate
        $tab = $request->query('tab');
        $data = ['targetUser' => $user];
        switch ($tab) {
            case null:
                return Inertia('User/Overview', $data);
            case 'packages':
                return Inertia('User/Packages', $this->getPackages($user, $request));
            case 'stars':
                return Inertia('User/Stars', $data);
        }
    }

    public function getPackages($user, $request)
    {
        $filters = $request->all('q', 'type', 'sort');

        if (is_null($filters['sort'])) {
            $filters['sort'] = 'last_updated';
        }

        return  [
            'filters' => $filters,
            'packages' => $user->package()
                ->filter($filters)
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($package) => [
                    'id' => $package->id,
                    'title' => $package->title,
                    'updated_at' => $package->updated_at,
                ])
        ];
    }
}
