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
        $fromPackageCommitIds = $fromPackage->commits()->oldest()->get()->map(function($commit){
            return $commit->id;
        })->toArray();
        $toPackageCommitIds = $toPackage->commits()->oldest()->get()->map(function($commit){
            return $commit->id;
        })->toArray();
        $diff_from_to = array_diff($fromPackageCommitIds, $toPackageCommitIds);
        $diff_to_from = array_diff($toPackageCommitIds, $fromPackageCommitIds);

        return compact('diff_from_to', 'diff_to_from');
    }
}
