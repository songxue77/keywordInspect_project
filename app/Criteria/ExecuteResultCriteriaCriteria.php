<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ExecuteResultCriteriaCriteria.
 *
 * @package namespace App\Criteria;
 */
class ExecuteResultCriteriaCriteria implements CriteriaInterface
{
    private $searchParams;

    public function __construct($searchParams)
    {
        $this->searchParams = $searchParams;
    }

    /**
     * Apply criteria in query repository
     *
     * @param  string  $model
     * @param  RepositoryInterface  $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (isset($this->searchParams['SearchKeyword'])) {
            $model = $model->where('SearchKeyword', $this->searchParams['SearchKeyword']);
        }

        if (isset($this->searchParams['SearchBeginDate'])) {
            $model = $model->where('RegDatetime', '>=', $this->searchParams['SearchBeginDate'].' 00:00:00');
        }

        if (isset($this->searchParams['SearchEndDate'])) {
            $model = $model->where('RegDatetime', '<=', $this->searchParams['SearchEndDate'].' 23:59:59');
        }

        return $model;
    }
}
