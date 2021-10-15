<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

}
