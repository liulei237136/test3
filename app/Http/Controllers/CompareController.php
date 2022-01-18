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
        $parentCommitIds = $parent->commits()->oldest()->get(['id'])->map(function ($commit) {return $commit->id;});
        $parentStr = $parentCommitIds->implode('-');
        $childCommitIds = $child->commits()->oldest()->get(['id'])->map(function ($commit) {return $commit->id;});
        $childStr = $childCommitIds->implode('-');
        info('111111111111111');
        info($parentCommitIds);
        info($childCommitIds);
        info($parentStr);
        info($childStr);

        // if($parentCommitIds->isEmpty() )
        //parent 领先或者相等与child
        if ($childStr == '' || Str::contains($parentStr, $childStr)) {
            return [
                'pass' => false,
                'message' => 'up_to_date',
                'id' => '123',
            ];
            //child 领先 parent
        } else if ($parentStr == '' || Str::contains($childStr, $parentStr)) {
            $diffCommitIds = collect(explode('-', ltrim($childStr, $parentStr . '-')))->map(function ($id) {
                return (int) $id;
            })->toArray();

            $diffCommits = Commit::with('author')->findMany($diffCommitIds,['id','title','author_id','audio_ids', 'created_at']);
            $contributers_count =$diffCommits->map(function($commit){return $commit->author_id;})->count();
            info('$contributers_count');
            info($contributers_count);
            $latestChildCommit = $diffCommits->last();
            $latestParentCommit = Commit::find($parentCommitIds->last());
            info('child' . $latestChildCommit);
            info('parent' . $latestParentCommit);
            $childAudioIds = $latestChildCommit ? json_decode($latestChildCommit->audio_ids) : array();
            $parentAudioIds = $latestParentCommit ? json_decode($latestParentCommit->audio_ids) : array();
            info('child');
            info($childAudioIds);
            info('parent');
            info($parentAudioIds);
            $insertAudio = Audio::find(array_diff($childAudioIds, $parentAudioIds));
            $deleteAudio = Audio::find(array_diff($parentAudioIds, $childAudioIds));
            info('insertAudio');
            info($insertAudio);
            info('deleteAudio');
            info($deleteAudio);
            return [
                'pass' => true,
                'commits' => $diffCommits,
                'deleteAudio' => $deleteAudio,
                'insertAudio' => $insertAudio,
                'contributers_count' => $contributers_count,
                'id' => '456',
            ];
        } else {
            //有不一致
            // $parentInserts = $parentCommitIds->diff($childCommitIds);
            return [
                'pass' => false,
                'message' => 'conflict',
                'id' => '789',
            ];
        }

        //todo
        // $diff_from_to = array_diff($childCommitIds, $parentCommitIds);
        // $diff_to_from = array_diff($parentCommitIds, $childCommitIds);

        // return compact('diff_from_to', 'diff_to_from');

    }
}
