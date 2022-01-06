<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CompareController extends Controller
{
    //
    public function compare(){
        return Inertia::render('Test.vue');
    }

    public function package(){
        return Inertia::render('Test.vue');
    }
}
