<?php

declare(strict_types=1);

namespace App\Services\Eduplan;

use App\Libraries\RestApi;
use GuzzleHttp\Client;
use Log;
use Exception;

class ExternalApiCallService
{
    private const TRY_COUNT = 3;
    private $restApi;

    public function __construct()
    {
        $this->restApi = new RestApi(
            config('common.NAVER_BASE_URL'),
            config('common.NAVER_API_KEY'),
            config('common.NAVER_SECRET_KEY'),
            config('common.NAVER_CUSTOMER_ID')
        );
    }

    public function getKeywordQCByAPI(array $keywords)
    {
        $keywordQCResult = [];
        $keywordCount = count($keywords);

        for ($i = 0; $i < self::TRY_COUNT; $i++) { // API Call 실패를 대비해 3번 TRY한다
            // Naver API parameter 호출 시 키워드에 빈칸이 있으면 오류 발생, 미리 제거
            $keywordsBlankRemoved = array_map(function ($item) {
                return strtoupper(str_replace([' ', ','], '', $item));
            }, $keywords);

            $apiResult = $this->restApi->get('/keywordstool', [
                'hintKeywords' => implode(',', $keywordsBlankRemoved)
            ]);

            if ($apiResult['code'] === '0000') {
                if (isset($apiResult['data']['code'])) {
                    Log::info('relKeyword API Call failed, try count: '.($i + 1).', keyword: '.json_encode($keywords).', message:'.$apiResult['message']);
                    usleep(500000);
                    continue;
                }

                for ($j = 0; $j < $keywordCount; $j++) {
                    if (isset($apiResult['data']['keywordList'][$j])) {
                        $keywordName = $apiResult['data']['keywordList'][$j]['relKeyword'];

                        $keywordQCResult[$keywordName]['pc'] = $apiResult['data']['keywordList'][$j]['monthlyPcQcCnt'] === '< 10' ?
                            0 : $apiResult['data']['keywordList'][$j]['monthlyPcQcCnt'];
                        $keywordQCResult[$keywordName]['mobile'] = $apiResult['data']['keywordList'][$j]['monthlyMobileQcCnt'] === '< 10' ?
                            0 : $apiResult['data']['keywordList'][$j]['monthlyMobileQcCnt'];
                    } else {
                        Log::info('keyword api call keyword list: '.implode(',', $keywords).' result: '.json_encode($apiResult));
                        foreach ($keywordsBlankRemoved as $keyword) { // API호출 실패 시 5개 키워드 월간조회 수 모두 0으로 설정
                            $keywordQCResult[$keyword]['pc'] = 0;
                            $keywordQCResult[$keyword]['mobile'] = 0;
                        }
                    }
                }

                break;
            }

            Log::info('relKeyword API Call failed, try count: '.($i + 1).', keyword: '.json_encode($keywords).', message:'.$apiResult['message']);
        }

        return $keywordQCResult;
    }

    public function getCafeWriterId(
        int $cafeId,
        string $cafePostIdWithQueryString
    ) {
        try {
            $client = new Client();
            $baseUri = 'http://apis.naver.com/cafe-web/cafe-articleapi/v2/cafes/'.$cafeId.'/articles/'.$cafePostIdWithQueryString;
            $apiResult = $client->request('GET', $baseUri);

            $statusCode = $apiResult->getStatusCode();
            if ($statusCode === 200) {
                $contents = $apiResult->getBody()->getContents();
                $responseData = json_decode($contents, true);

                if (isset($responseData['result']['errorCode'])) {
                    // error occured, no action
                    $writerId = '';
                } else {
                    $writerId = $responseData['result']['article']['writer']['id'] ?? '';
                }
            }
        } catch (Exception $e) {
            Log::error('Get Cafe Info API Throws Exception:'.$e->getMessage());
            $writerId = '';
        }

        return $writerId;
    }
}
