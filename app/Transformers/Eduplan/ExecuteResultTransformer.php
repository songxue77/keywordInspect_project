<?php

namespace App\Transformers\Eduplan;

use League\Fractal\TransformerAbstract;
use App\Entities\Eduplan\ExecuteResult;

/**
 * Class ExecuteResultTransformer.
 *
 * @package namespace App\Transformers\Eduplan;
 */
class ExecuteResultTransformer extends TransformerAbstract
{
    /**
     * Transform the ExecuteResult entity.
     *
     * @param \App\Entities\Eduplan\ExecuteResult $model
     *
     * @return array
     */
    public function transform(ExecuteResult $model)
    {
        return [
            'ExecuteResultIdx'      => (int) $model->ExecuteResultIdx,
            'SearchKeyword'         => $model->SearchKeyword,
            'KeywordCnt'            => $model->KeywordCnt,
            'IsKeywordGroupResult'  => $model->IsKeywordGroupResult,
            'AdminID'               => $model->AdminID,
            'AdminIdx'              => $model->AdminIdx,
            'RegDatetime'           => $model->RegDatetime,
            'ExecuteResult'         => $model->ExecuteResult
        ];
    }
}
