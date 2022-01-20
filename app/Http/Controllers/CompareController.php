<?php

namespace App\Http\Controllers;

use App\Http\Traits\CommonInfoTrait;
use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Hamcrest\Core\Set;
use Illuminate\Support\Str;
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

        $data['parent'] = $parent;
        $data['child'] = $child;
        $data['diff'] = $this->diffPackage($parent, $child);

        return Inertia::render('Compare/ComparePackage', $data);
    }

    protected function diffPackage(Package $parent, Package $child)
    {
        $parentCommitIds = $parent->commits()->oldest()->get(['id'])->map(function ($commit) {
            return $commit->id;
        });
        $parentStr = $parentCommitIds->implode('-');
        $childCommitIds = $child->commits()->oldest()->get(['id'])->map(function ($commit) {
            return $commit->id;
        });
        $childStr = $childCommitIds->implode('-');
        info('$parentStr');
        info($parentStr);
        info('$childStr');
        info($childStr);

        //parent 领先或者相等与child
        if ($childStr === $parentStr) {
            info('identical');
            return [
                'result' => 'identical',
            ];
        } else if (str_contains($parentStr, $childStr)) {
            info('behind');
            return [
                'result' => 'behind',
            ];
            //child 领先 parent
        } else if (str_contains($childStr, $parentStr)) {
            info('front');
            $data = $this->diffCommits($parentCommitIds, $childCommitIds);
            $data['result'] = 'front';
            return $data;
        } else {
            //有不一致
            info('conflict');
            $data = $this->diffCommits($parentCommitIds, $childCommitIds);
            $data['result'] = 'conflict';
            return $data;
        }
    }

    protected function diffCommits($parentCommitIds, $childCommitIds)
    {
        $childBeforeCommits = Commit::with('author')->findMany($childCommitIds->diff($parentCommitIds));
        $latestParentCommit = Commit::find($parentCommitIds->last()); //todo use largest of

        $contributersCount = $childBeforeCommits->map(function ($commit) {
            return $commit->author_id;
        })->unique()->count();

        $latestChildCommit = $childBeforeCommits->sortByDesc(function ($c) {
            return $c->id;
        })->first();

        $childAudioIds = json_decode($latestChildCommit->audio_ids);
        $parentAudioIds = json_decode($latestParentCommit->audio_ids);
        $insertAudio = Audio::find(array_diff($childAudioIds, $parentAudioIds));
        $deleteAudio = Audio::find(array_diff($parentAudioIds, $childAudioIds));

        $data = compact('childBeforeCommits', 'contributersCount');

        !empty($insertAudio) && $data['insertAudio'] = $insertAudio;
        !empty($deleteAudio) && $data['deleteAudio'] = $deleteAudio;

        return $data;
    }
}
