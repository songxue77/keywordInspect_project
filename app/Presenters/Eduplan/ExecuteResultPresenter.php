<?php

namespace App\Presenters\Eduplan;

use App\Transformers\Eduplan\ExecuteResultTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ExecuteResultPresenter.
 *
 * @package namespace App\Presenters\Eduplan;
 */
class ExecuteResultPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ExecuteResultTransformer();
    }
}
