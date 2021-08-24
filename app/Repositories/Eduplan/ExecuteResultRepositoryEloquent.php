<?php

namespace App\Repositories\Eduplan;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Eduplan\ExecuteResultRepository;
use App\Entities\Eduplan\ExecuteResult;
use App\Validators\ExecuteResultValidator;

/**
 * Class ExecuteResultRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eduplan;
 */
class ExecuteResultRepositoryEloquent extends BaseRepository implements ExecuteResultRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ExecuteResult::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //$this->pushCriteria(app(RequestCriteria::class));
    }

    public function presenter()
    {
        return "App\\Presenters\\Eduplan\\ExecuteResultPresenter";
    }
}
