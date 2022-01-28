<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Http\Request;

class UserPackagesFilter
{
    protected $request;
    protected $queryBuilder;
    protected $filters = ['type', 'sort'];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->queryBuilder = $builder;

        // 得到 $this->>filters 属性中存在且在请求中传入的过滤条件
        $filters = array_filter($this->request->only($this->filters));

        foreach ($filters as $filter => $value) {
            // 在此处，$filter 即为方法名
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->queryBuilder;
    }

    protected function type($type)
    {
        switch ($type) {
            case 'public':
                $this->queryBuilder->whereNull('private');
                break;
            case 'private':
                $this->queryBuilder->whereNotNull('private');
                break;
            case 'sources':
                $this->queryBuilder->whereNull('parent_id');
                break;
            case 'forks':
                $this->queryBuilder->whereNotNUll('parent_id');
                break;
        }
    }

    protected function sort($sort)
    {
        switch ($sort) {
            case 'latest_update':
                $this->queryBuilder->latest('updated_at');
                break;
            case 'title':
                $this->queryBuilder->orderBy('title');
                break;
            case 'stars':

                break;
        }
    }

    // protected function by($username)
    // {
    //     $user = User::where('name', $username)->firstOrfail();

    //     $this->queryBuilder->where('user_id', $user->id);
    // }

    // public function popularity()
    // {
    //     $this->queryBuilder->orderBy('answers_count', 'desc');
    // }

    // public function unanswered()
    // {
    //     $this->queryBuilder->where('answers_count', '=', 0);
    // }
}
