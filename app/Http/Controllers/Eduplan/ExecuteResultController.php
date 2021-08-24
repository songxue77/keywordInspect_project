<?php

declare(strict_types=1);

namespace App\Http\Controllers\Eduplan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Eduplan\ExecuteResultService;

class ExecuteResultController extends Controller
{
    private $executeResultService;

    public function __construct(
        ExecuteResultService $executeResultService
    ) {
        $this->executeResultService = $executeResultService;
    }

    /**
     * 신규 키워드 조회 페이지
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->executeResultService->view();
    }

    /**
     * 키워드 조회 결과 페이지
     *
     * @param $executeResultIdx
     * @param  Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($executeResultIdx, Request $request)
    {
        return $this->executeResultService->show($executeResultIdx, $request);
    }

    /**
     * 이전 조회 결과 페이지
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function list()
    {
        return $this->executeResultService->list();
    }

    /**
     * 이전 조회 결과 데이터 Ajax로 읽기
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAjax(Request $request)
    {
        return $this->executeResultService->listAjax($request);
    }

    /**
     * 키워드 검수 Process
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function process(Request $request)
    {
        return $this->executeResultService->process($request);
    }

    /**
     * 채널 점유비율 계산 by 키워드 검수 결과
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        return $this->executeResultService->statistics($request);
    }

    /**
     * 키워드 검수 결과 Excel 출력
     * 
     * @param  Request  $request
     */
    public function excelExport(Request $request)
    {
        return $this->executeResultService->excelExport($request);
    }
}
