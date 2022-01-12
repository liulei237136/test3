<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Traits\CommonInfoTrait;

class CompareController extends Controller
{
    use CommonInfoTrait;
    //
    public function compare(){
        return Inertia::render('Test.vue');
    }

    public function package(Package $toPackage, Package $fromPackage){
        $data = $this->commonInfo($toPackage);

        $diff = $this->diffPackage($toPackage, $fromPackage);

        $data['toPackage'] = $toPackage;
        $data['fromPackage'] = $fromPackage;
        $data['diff'] = $diff;

        return Inertia::render('Compare/ComparePackage', $data);
    }

    protected function diffPackage(Package $toPackage, Package $fromPackage){
        // $diff = array();
        // $toPath =array_diff( $toPackage->path);
        // $fromPath = $fromPackage->path;
        $fromCommits = $fromPackage->commits()->latest()->get(['id']);
        info(json_encode($fromCommits));
        // $childDiffParent = array_diff($fromPackage->path, $toPackage->path);
        // $arr2 = array(1,2,3,5);
        // $diff = array_diff($arr)

    }
}
