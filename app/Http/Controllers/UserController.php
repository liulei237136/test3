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
                return Inertia('User/Packages', $data);
            case 'stars':
                return Inertia('User/Stars', $data);
        }
    }
}
