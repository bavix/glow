<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\Controller;

abstract class BaseController extends Controller
{

    /**
     * @var string
     */
    protected $sortable = ['id'];

    /**
     * @param Request $request
     * @return Builder
     */
    abstract protected function query(Request $request): Builder;

    /**
     * @param Request $request
     * @return QueryBuilder
     */
    protected function queryBuilder(Request $request): QueryBuilder
    {
        return QueryBuilder::for($this->query($request), $request)
            ->defaultSort(...$this->sortable);
    }

}
