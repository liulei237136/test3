<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index()
    {
        //todo 区分private 还有自己的项目
        $query = request()->all(['term']);
        if($query['term']){
            $package = Package::where('name', 'like', '%' . $query['term'] . '%')->paginate();
        }else{
            $package = Package::paginate();
        }
        return Inertia::render('Search', ['package'=> $package,'query' => $query]);
    }
}
