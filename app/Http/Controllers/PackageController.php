<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

    public function create(){
        return Inertia::render('Package/Create');
    }

    public function init(Package $package){
        return Inertia::render('Package/Init', ['package' => $package]);
    }

    public function store(Request $request){
        $input = $request->all();
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'category' => Rule::in(['毛毛虫', '小达人', '卷之友']),
            'description' => ['required', 'string', 'max:3000'],
        ])->validate();

        $package = Package::create([
            'name' => $input['name'],
            'category' => $input['category'],
            'description' => $input['description'],
            'author_id' => auth()->id(),
        ]);

        // return redirect()->route('package.init',['package' => $package]);
        // return Inertia::render('Package/Init', ['package' => $package]);
        return $package;
    }

    public function edit($package){
        return Inertia::render('Package/Edit', ['package' => Package::with('audio')->findOrFail($package)]);
    }


}
