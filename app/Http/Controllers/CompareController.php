<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonInfoTrait;
use App\Models\Package;
use Inertia\Inertia;

class CompareController extends Controller
{
    use CommonInfoTrait;
    //
    public function compare(Package $package)
    {
        return Inertia::render('Compare/Compare', compact('package'));
    }

    public function package(Package $parent, Package $child)
    {
        $data = $this->commonInfo($parent);

        // $diff = $this->diffPackage($parent, $child);

        $data['parent'] = $parent;
        $data['child'] = $child;
        // $data['diff'] = $diff;

        return Inertia::render('Compare/ComparePackage', $data);
    }

    protected function diffPackage(Package $parent, Package $child)
    {
        $parentCommits = $parent->commits()->oldest()->get()->toArray();
        $parentCommitIdStr = array_map(function ($commit) {return $commit['id'];}, $parentCommits);
        $childCommits = $child->commits()->oldest()->get()->toArray();
        $childCommitIdStr = array_map(function ($commit) {return $commit['id'];}, $childCommits);

        //todo
        // $diff_from_to = array_diff($childCommitIds, $parentCommitIds);
        // $diff_to_from = array_diff($parentCommitIds, $childCommitIds);

        // return compact('diff_from_to', 'diff_to_from');

    }
}
