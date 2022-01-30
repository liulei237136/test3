<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->paginate(10);

        return Inertia::render('Index', compact('packages'));
    }
}
