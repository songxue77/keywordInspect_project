<?php

declare(strict_types=1);

namespace App\Services\Eduplan;

use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Exception;
use Cache;
use Log;
use DB;
use App\Repositories\Eduplan\SiteRepository;
use App\Repositories\Eduplan\ExecuteResultRepository;
use App\Repositories\Eduplan\KeywordGroupRepository;
use App\Repositories\Eduplan\WriterIDRepository;
use App\Services\Eduplan\ExternalApiCallService;
use App\Services\Eduplan\CrawlingService;
use App\Criteria\ExecuteResultCriteriaCriteria;
use App\Traits\Eduplan\StatisticsDataTransformer;
use App\Exports\ExecuteResultExport;
use App\Libraries\ColorResolver;

class ExecuteResultService
{
    use StatisticsDataTransformer;

    private CONST A_HEAD_SITE_COUNT = 5;
    
    private $siteRepository;
    private $executeResultRepository;
    private $keywordGroupRepository;
    private $writerIDRepository;
    private $externalApiCallService;
    private $crawlingService;

    public function __construct(
        SiteRepository $siteRepository,
        ExecuteResultRepository $executeResultRepository,
        KeywordGroupRepository $keywordGroupRepository,
        WriterIDRepository $writerIDRepository,
        ExternalApiCallService $externalApiCallService,
        CrawlingService $crawlingService
    ) {
        $this->siteRepository = $siteRepository;
        $this->executeResultRepository = $executeResultRepository;
        $this->keywordGroupRepository = $keywordGroupRepository;
        $this->writerIDRepository = $writerIDRepository;
        $this->externalApiCallService = $externalApiCallService;
        $this->crawlingService = $crawlingService;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $relatedDatas = $this->getRelatedDataForView();

        return view('eduplan.executeResult.index', [
            'mode' => 'index',
            'executeResultIdx' => '',
            'executeSearchKeyword' => '',
            'executeResult' => [],
            'executeResultArray' => [],
            'executeKeywordCount' => 0,
            'keywordGroups' => $relatedDatas['keywordGroups'],
            'writerIds' => $relatedDatas['writerIds'],
            'sites' => $relatedDatas['sites']
        ]);
    }

    /**
     * @param $executeResultIdx
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($executeResultIdx, $request)
    {
        try {
            $executeResult = $this->executeResultRepository->find($executeResultIdx);
            $executeResultArray = json_decode($executeResult['data']['ExecuteResult'], true);
            $executeKeywordCount = $executeResult['data']['KeywordCnt'];
            $executeSearchKeyword = $executeResult['data']['SearchKeyword'];

            $relatedDatas = $this->getRelatedDataForView();
        } catch (Exception $e) {
            Log::error('ExecuteResult view page error occurred, code: '.$e->getCode().', message: '.$e->getMessage());

            return redirect('/eduplan/execute');
        }

        return view('eduplan.executeResult.index', [
            'mode' => $request['mode'],
            'executeResultIdx' => $executeResultIdx,
            'executeSearchKeyword' => $executeSearchKeyword,
            'executeResult' => $executeResult,
            'executeResultArray' => $executeResultArray,
            'executeKeywordCount' => $executeKeywordCount,
            'keywordGroups' => $relatedDatas['keywordGroups'],
            'writerIds' => $relatedDatas['writerIds'],
            'sites' => $relatedDatas['sites']
        ]);
    }

    public function excelExport($request)
    {
        try {
            $executeResult = $this->executeResultRepository->find($request['executeResultIdx']);
            $executeResultArray = json_decode($executeResult['data']['ExecuteResult'], true);
            $executeKeywordCount = $executeResult['data']['KeywordCnt'];
        } catch (Exception $e) {
            Log::error('ExecuteResult view page error occurred, code: '.$e->getCode().', message: '.$e->getMessage());

            return redirect('/eduplan/execute');
        }

        return Excel::download(new ExecuteResultExport([
            'executeResultIdx' => $request['executeResultIdx'],
            'executeResult' => $executeResult,
            'executeResultArray' => $executeResultArray,
            'executeKeywordCount' => $executeKeywordCount
        ]), 'execute_result_'.date('Ymd').'.xlsx');
    }

    /**
     * ????????? ??????, ????????? ?????????, ????????? ????????? ??????
     *
     * @return array
     */
    public function getRelatedDataForView()
    {
        // ????????? ??????
        $keywordGroups = $this->keywordGroupRepository->orderBy('RegDatetime', 'DESC')->findWhere([
            'IsUse' => 1
        ]);
        // ????????? ?????????
        $writerId = $this->writerIDRepository->skipPresenter()->first();
        $writerIds = $writerId ? json_decode($writerId['WriterIDData'], true) : [];
        // ????????? ?????????
        $sites = $this->siteRepository->all();

        return [
            'keywordGroups' => $keywordGroups,
            'writerIds' => $writerIds,
            'sites' => $sites
        ];
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function process($request)
    {
        if ($request['keywordGroupIdx']) {
            $keywordGroupIdx = $request['keywordGroupIdx'];
            $keywordGroup = $this->keywordGroupRepository->find($keywordGroupIdx);

            // ????????? ?????? ?????????
            $keywords = [];
            foreach ($keywordGroup['data']['keywords'] as $keywordGroupKeyword) {
                $keywords[] = $keywordGroupKeyword['Keyword'];
            }
        } else {
            $keywordGroupIdx = 0;
            $keywords = [
                $request['keywordName']
            ];
        }

        // ????????? ?????? ????????? ?????????
        $sites = $this->siteRepository->all();
        // ?????? ????????? (??????, ??????)
        $cafes = $this->getCafesFromSites($sites);
        // ????????? ?????? ?????????
        $writerIdResult = $this->writerIDRepository->skipPresenter()->first();
        $writerIds = $writerIdResult ? json_decode($writerIdResult['WriterIDData'], true) : [];

        // 1. ????????? API ???????????? PC,Mobile ????????? ??????
        $pcAndMobileViewCntValue = $this->getKeywordQcValue($keywords);

        $jsonData = [];
        $datetime = date('Y-m-d H:i:s');
        // 2. ????????? ?????? ????????? ?????????
        foreach ($keywords as $keyword) {
            $keyword = (string)$keyword;
            $keywordForViewCnt = strtoupper(str_replace(' ', '', $keyword));
            $pcViewCntValue = $pcAndMobileViewCntValue[$keywordForViewCnt]['pc'] ?? 0;
            $mobileViewCntValue = $pcAndMobileViewCntValue[$keywordForViewCnt]['mobile'] ?? 0;
            $totalViewCntValue = $pcViewCntValue + $mobileViewCntValue;
            $rowData = [
                'KeywordGroupIdx' => $keywordGroupIdx,
                'inspectDatetime' => $datetime,
                'code' => '0000',
                'message' => '????????????',
                'result' => [
                    'keyword' => $keyword,
                    'monthlyPcQcCnt' => $pcViewCntValue,
                    'monthlyMobileQcCnt' => $mobileViewCntValue,
                    'monthlyTotalQcCnt' => $totalViewCntValue,
                    'aHeadSiteType' => '',
                    'sectionRank' => '',
                    'cafeTopRank' => 0,
                    'isAdShowTop' => false,
                    'siteLink' => [],
                    'cafeLink' => []
                ]
            ];

            $searchViewCafeResult = $this->crawlingService->getNaverSearchViewCafePageResult($keyword);

            if ($searchViewCafeResult) {
                // 1??? ~ 5??? (??????)
                $rowData['result']['cafeLink'] = $this->getCafeLinkListByCrawlingResult($searchViewCafeResult, $cafes['all'], $writerIds);
            }

            $searchViewAllResult = $this->crawlingService->getNaverSearchViewAllPageResult($keyword);

            if ($searchViewAllResult) {
                // ???????????? ????????????
                $rowData['result']['isAdShowTop'] = $this->checkAdInSiteLink($searchViewAllResult);

                // ?????? ?????? ??????
                $rowData['result']['sectionRank'] = $this->calculateSectionRankByCrawlingResult($searchViewAllResult);

                // ????????? href ?????????
                $siteLinkList = $this->getSiteLinkListByCrawlingResult($searchViewAllResult);

                // ?????? ??????
                $rowData['result']['aHeadSiteType'] = $this->getAHeadSiteTypeByCrawlingResult($siteLinkList);
                
                // ??????????????? ???????????????
                $rowData['result']['cafeTopRank'] = $this->getCafeTopRank($siteLinkList);

                // 1??? ~ 5???
                $rowData['result']['siteLink'] = $this->getSiteLinkResult($siteLinkList, $sites['data']);
            }

            $jsonData['ProcessResult'][] = $rowData;
            $jsonData['ProcessInfo'] = [
                'KeywordGroupIdx' => $request['keywordGroupIdx'] ? $keywordGroup['data']['KeywordGroupIdx'] : 0
            ];
        }

        // 3. DB INSERT
        $connection = DB::connection();
        $connection->beginTransaction();

        try {
            $insertResult = $this->executeResultRepository->create([
                'SearchKeyword' => $request['keywordGroupIdx'] ? $keywordGroup['data']['KeywordGroupName'] : $request['keywordName'],
                'KeywordCnt' => count($keywords),
                'IsKeywordGroupResult' => $request['keywordGroupIdx'] ? 1 : 0,
                'AdminID' => $request->session()->has('adminID') ? $request->session()->get('adminID') : 'songxue77',
                'AdminIdx' => $request->session()->has('adminIdx') ? $request->session()->get('adminIdx') : 4,
                'RegDatetime' => $datetime,
                'ExecuteResult' => json_encode($jsonData)
            ]);

            $connection->commit();
            $result = [
                'code' => '0000',
                'message' => '?????? ??????',
                'data' => [
                    'executeResultIdx' => $insertResult['data']['ExecuteResultIdx']
                ]
            ];
        } catch (Exception $e) {
            $connection->rollBack();
            Log::error($e->getFile().' : '.$e->getLine().' line : '.$e->getMessage());
            $result = [
                'code' => '9999',
                'message' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    /*
     * ?????? ?????? ???????????? ??????
     */
    public function statistics($request)
    {
        $executeResultIdx = $request['executeResultIdx'];
        $cafeName = $request['cafeName'];
        $isOwn = $request['isOwn'];

        $executeResult = $this->executeResultRepository->find($executeResultIdx);

        $executeResultArray = json_decode($executeResult['data']['ExecuteResult'], true);
        $keywordCount = count($executeResultArray['ProcessResult']);
        $searchTotalResultCount = $keywordCount * 5;

        $viewTopRankCount = 0; // View 1??? ?????? ??? 
        $viewTotalCount = 0; // View ?????? ?????? ???
        $viewTotalCafeCount = 0; // View ?????? ?????? ???
        $viewTotalPostCount = 0; // View ????????? ?????? ???
        $viewTotalBlogCount = 0; // View ????????? ?????? ???
        $cafeTopRankCount = 0; // ?????? 1??? ?????? ???
        $cafeTotalRankCount = 0; // ?????? ?????? ?????? ???
        if ($keywordCount > 0) {
            foreach ($executeResultArray['ProcessResult'] as $executeKey => $executeValue) {
                foreach ($executeValue['result']['cafeLink'] as $cafeKey => $cafeLink) {
                    if ($cafeLink['Name'] === $cafeName) {
                        $cafeTotalRankCount++;

                        if ($cafeKey === 0) {
                            $cafeTopRankCount++;
                        }
                    }
                }

                foreach ($executeValue['result']['siteLink'] as $siteKey => $siteLink) {
                    if ($siteLink['Name'] === $cafeName) {
                        $viewTotalCount++;

                        if ($siteKey === 0) {
                            $viewTopRankCount++;
                        }

                        if ($siteLink['SiteType'] === 'Cafe') {
                            $viewTotalCafeCount++;
                        } elseif ($siteLink['SiteType'] === 'Post') {
                            $viewTotalPostCount++;
                        } elseif ($siteLink['SiteType'] === 'Blog') {
                            $viewTotalBlogCount++;
                        }
                    }
                }
            }
        }

        $statistics = [
            'cafeName' => $cafeName,
            'viewTopRankCount' => $viewTopRankCount,
            'viewTotalCount' => $viewTotalCount,
            'viewTotalCafeCount' => $viewTotalCafeCount,
            'viewTotalPostCount' => $viewTotalPostCount,
            'viewTotalBlogCount' => $viewTotalBlogCount,
            'cafeTopRankCount' => $cafeTopRankCount,
            'cafeTotalRankCount' => $cafeTotalRankCount,
            'viewTopRankCountText' => $this->transformViewTopRankCountToText($viewTopRankCount, $keywordCount),
            'viewTotalCountText' => $this->transformViewTotalCountToText($viewTotalCount, $searchTotalResultCount),
            'viewTotalCafeCountText' => $this->transformViewTotalCafeCountToText($viewTotalCafeCount, $searchTotalResultCount),
            'viewTotalPostCountText' => $this->transformViewTotalPostCountToText($viewTotalPostCount, $searchTotalResultCount),
            'viewTotalBlogCountText' => $this->transformViewTotalBlogCountToText($viewTotalBlogCount, $searchTotalResultCount),
            'cafeTopRankCountText' => $this->transformCafeTopRankCountToText($cafeTopRankCount, $keywordCount),
            'cafeTotalCountText' => $this->transformCafeTotalCountToText($cafeTotalRankCount, $searchTotalResultCount)
        ];

        $jsonData = [];
        $jsonData['ProcessResult'] = $executeResultArray['ProcessResult'];
        $jsonData['ProcessInfo'] = $executeResultArray['ProcessInfo'];
        if ($isOwn === '1') {
            $jsonData['StatisticsResult']['isOwn'] = $statistics;
            $jsonData['StatisticsResult']['isNotOwn'] = isset($executeResultArray['StatisticsResult']['isNotOwn']) ? $executeResultArray['StatisticsResult']['isNotOwn'] : [];
        } else {
            $jsonData['StatisticsResult']['isNotOwn'] = $statistics;
            $jsonData['StatisticsResult']['isOwn'] = isset($executeResultArray['StatisticsResult']['isOwn']) ? $executeResultArray['StatisticsResult']['isOwn'] : [];
        }

        $jsonData['StatisticsResult']['compare'] = $this->transformCompareViewTopRankToText($jsonData['StatisticsResult']);

        // 3. DB INSERT
        $connection = DB::connection();
        $connection->beginTransaction();

        try {
            $updateResult = $this->executeResultRepository->update([
                'ExecuteResult' => json_encode($jsonData)
            ], $executeResultIdx);

            $connection->commit();
            $result = [
                'code' => '0000',
                'message' => '?????????????????????.',
                'data' => [
                    'executeResultIdx' => $executeResultIdx,
                    'statistics' => $statistics,
                    'compare' => $jsonData['StatisticsResult']['compare']
                ]
            ];
        } catch (Exception $e) {
            $connection->rollBack();
            Log::error($e->getFile().' : '.$e->getLine().' line : '.$e->getMessage());
            $result = [
                'code' => '9999',
                'message' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }

    public function getCafesFromSites(array $sites): array
    {
        $cafeAll = [];
        $cafeOurs = [];

        if (count($sites['data']) > 0) {
            foreach ($sites['data'] as $data) {
                if ($data['KeywordSiteType'] === 'Cafe') {
                    $cafeAll[] = $data;

                    if ($data['IsOwner'] === 1) {
                        $cafeOurs[] = $data['SiteURL'];
                    }
                }
            }
        }

        return [
            'all' => $cafeAll,
            'ours' => $cafeOurs
        ];
    }

    public function getKeywordQcValue(array $keywords): array
    {
        // ?????? API??? ????????? 5?????? ?????? ??????????????? chunk
        $keywordsChunkByFive = array_chunk($keywords, 5);

        $keywordQC = [];
        foreach ($keywordsChunkByFive as $keywordsChunked) {
            $keywordQCArray = $this->externalApiCallService->getKeywordQCByAPI($keywordsChunked);
            usleep(500000);

            if (count($keywordQCArray) > 0) { // API Call ???????????? ??? ????????? ?????? ??????
                foreach ($keywordQCArray as $keyword => $value) {
                    $keywordQC[$keyword] = $value;
                }
            }
        }

        return $keywordQC;
    }

    public function checkAdInSiteLink(Crawler $searchViewAllResult): bool
    {
        $powerLinkList = $searchViewAllResult->filter('li._svp_item')->each(function ($node) {
            return $node->attr('data-cr-area');
        });

        /**
         * rvw*o : ??????
         * rvw*b : ?????????
         * rvw*c : ??????
         * rvw*f : ???????????????
         * rvw*p : ?????????
         */
        if (count($powerLinkList) > 0) {
            return $powerLinkList[0] === 'rvw*o';
        }

        return false;
    }

    public function calculateSectionRankByCrawlingResult(Crawler $searchViewAllResult): int
    {
        $sectionMenuList = $searchViewAllResult->filter('li.menu')->each(function ($node) {
            return $node->text();
        });

        return array_search('VIEW', $sectionMenuList) ? (array_search('VIEW', $sectionMenuList) + 1) : 0;
    }

    public function getSiteLinkListByCrawlingResult(Crawler $searchViewAllResult): array
    {
        $siteLinkListAferRemoveAdLink = [];
        $siteLinkList = $searchViewAllResult->filter('a.total_tit')->each(function ($node) {
            return $node->attr('href');
        });

        $i = 0;
        foreach ($siteLinkList as $siteLink) {
            if ($i === 5) { // ?????? 5?????? ?????? (????????????)
                break;
            }
            
            if (Str::contains($siteLink, 'adcr.naver.com') === true) { // ?????? Url?????? ??????
                continue;
                //$siteLinkListAferRemoveAdLink[] = $this->crawlingService->getPageUrlByClickAdvertisementUrl($siteLink);
            } else {
                $siteLinkListAferRemoveAdLink[] = $siteLink;
                $i++;
            }
        }

        return $siteLinkListAferRemoveAdLink;
    }

    public function getAHeadSiteTypeByCrawlingResult(array $siteLinkList): string
    {
        $result = '??????';
        $blogCount = 0;
        $cafeCount = 0;

        $i = 0;
        foreach ($siteLinkList as $siteLink) {
            if (Str::contains($siteLink, 'blog.naver.com') === true) {
                $blogCount++;
            } elseif (Str::contains($siteLink, 'cafe.naver.com') === true) {
                $cafeCount++;
            } else {
                break;
            }

            if ($i === 4) { // ?????? 5?????? ??????
                break;
            }

            $i++;
        }

        if ($blogCount === self::A_HEAD_SITE_COUNT) {
            $result = '???';
        } elseif ($cafeCount === self::A_HEAD_SITE_COUNT) {
            $result = '???';
        }

        return $result;
    }

    public function getOurCafeTopRank(
        array $siteLinkList,
        array $cafeIsOurs
    ): int {
        $topRank = 0;
        foreach ($siteLinkList as $key => $site) {
            foreach ($cafeIsOurs as $cafe) {
                if (strpos($site, $cafe) !== false) {
                    $topRank = $key + 1;
                    break 2;
                }
            }
        }

        return $topRank;
    }

    public function getCafeTopRank(array $siteLinkList): int
    {
        $topRank = 0;
        foreach ($siteLinkList as $key => $site) {
            if (Str::contains($site, 'cafe.naver.com') === true) {
                $topRank = $key + 1;
                break;
            }
        }

        return $topRank;
    }

    public function getSiteLinkResult(
        array $siteLinkList,
        array $sites
    ): array {
        $siteDisplayArray = [];
        if (count($siteLinkList) > 0) {
            foreach ($siteLinkList as $siteLink) {
                $isRegisterd = false;
                $writerId = '';
                foreach ($sites as $site) {
                    if (Str::contains($siteLink, $site['SiteURL']) === true) {
                        if ($site['KeywordSiteType'] === 'Cafe') { // ????????????
                            $parseCafeLinkUrlResult = $this->parseCafeLinkUrl($siteLink);

                            if (Cache::has('CafeWriterIdByCafePostId:'.$parseCafeLinkUrlResult['id'])) {
                                $writerId = Cache::get('CafeWriterIdByCafePostId:'.$parseCafeLinkUrlResult['id']);
                            }
                        }

                        $siteDisplayArray[] = [
                            'Name' => $site['SiteName'],
                            'Color' => bin2hex($site['Color']),
                            'FontColor' => ColorResolver::getContrastColor('#'.bin2hex($site['Color'])),
                            'WriterId' => $writerId,
                            'SiteType' => $site['KeywordSiteType']
                        ];

                        $isRegisterd = true;
                        break;
                    }
                }

                if ($isRegisterd === false) {
                    $siteType = '??????';
                    $siteName = 'Other';
                    if (Str::contains($siteLink, 'blog.naver.com') === true) {
                        $siteName = '??????(B)';
                        $siteType = 'Blog';
                    } elseif (Str::contains($siteLink, 'cafe.naver.com') === true) {
                        $siteName = '??????(C)';
                        $siteType = 'Cafe';
                    } elseif (Str::contains($siteLink, 'post.naver.com') === true) {
                        $siteName = '??????(P)';
                        $siteType = 'Post';
                    }

                    $siteDisplayArray[] = [
                        'Name' => $siteName,
                        'Color' => '',
                        'FontColor' => 'black',
                        'WriterId' => '',
                        'SiteType' => $siteType
                    ];
                }
            }
        }

        // ??????????????? 5????????? ?????? ??????
        $siteDisplayCount = count($siteDisplayArray);
        if ($siteDisplayCount < 5) {
            for ($j = 0; $j < 5 - $siteDisplayCount; $j++) {
                $siteDisplayArray[] = [
                    'Name' => '??????(X)',
                    'Color' => '',
                    'FontColor' => 'black',
                    'WriterId' => '',
                    'SiteType' => ''
                ];
            }
        }

        return $siteDisplayArray;
    }

    public function getCafeLinkListByCrawlingResult(
        Crawler $searchViewCafeResult,
        array $cafes,
        array $writerIds
    ): array {
        $cafeLinkList = $searchViewCafeResult->filter('a.total_tit')->each(function ($node) {
            return $node->attr('href');
        });

        $i = 0;
        $cafeDisplayArray = [];
        if (count($cafeLinkList) > 0) {
            foreach ($cafeLinkList as $cafeLink) {
                $isCafeRegisterd = false;
                foreach ($cafes as $cafe) {
                    $writerId = '';
                    if (Str::contains($cafeLink, $cafe['SiteURL']) === true) {
                        $parseCafeLinkUrlResult = $this->parseCafeLinkUrl($cafeLink);

                        if (Cache::has('CafeWriterIdByCafePostId:'.$parseCafeLinkUrlResult['id'])) {
                            $writerId = Cache::get('CafeWriterIdByCafePostId:'.$parseCafeLinkUrlResult['id']);
                        } else {
                            if ($cafe['IsOwner'] === 1) {
                                $cafeWriterId = $this->externalApiCallService->getCafeWriterId($cafe['SiteNID'], $parseCafeLinkUrlResult['idWithQueryString']);
                                if (in_array($cafeWriterId, $writerIds) === true) {
                                    Cache::put('CafeWriterIdByCafePostId:'.$parseCafeLinkUrlResult['id'], $cafeWriterId, now()->addDays(30));
                                    $writerId = $cafeWriterId;
                                }
                            }
                        }

                        $cafeDisplayArray[] = [
                            'Name' => $cafe['SiteName'],
                            'Color' => bin2hex($cafe['Color']),
                            'FontColor' => ColorResolver::getContrastColor('#'.bin2hex($cafe['Color'])),
                            'WriterId' => $writerId,
                            'SiteType' => $cafe['KeywordSiteType']
                        ];

                        $isCafeRegisterd = true;
                        break;
                    }
                }

                if ($isCafeRegisterd === false) {
                    $cafeDisplayArray[] = [
                        'Name' => '??????(C)',
                        'Color' => '',
                        'FontColor' => 'black',
                        'WriterId' => '',
                        'SiteType' => 'Cafe'
                    ];
                }

                if ($i === 4) {
                    break;
                }

                $i++;
            }
        }
        
        // ??????????????? 5????????? ?????? ??????
        $cafeDisplayCount = count($cafeDisplayArray);
        if ($cafeDisplayCount < 5) {
            for ($j = 0; $j < 5 - $cafeDisplayCount; $j++) {
                $cafeDisplayArray[] = [
                    'Name' => '??????(X)',
                    'Color' => '',
                    'FontColor' => 'black',
                    'WriterId' => '',
                    'SiteType' => 'Cafe'
                ];
            }
        }

        return $cafeDisplayArray;
    }

    public function parseCafeLinkUrl($cafeLink): array
    {
        $cafeLinkPath = parse_url($cafeLink, PHP_URL_PATH);
        $cafeLinkQuery = parse_url($cafeLink, PHP_URL_QUERY);

        $cafeLinkPathArray = explode('/', $cafeLinkPath);
        
        return [
            'id' => end($cafeLinkPathArray),
            'idWithQueryString' => end($cafeLinkPathArray).'?'.$cafeLinkQuery
        ];
    }

    public function list()
    {
        return view('eduplan.executeResult.list', [
            'defaultBeginDate' => date('Y-m-d', strtotime('-1 week')),
            'defaultEndDate' => date('Y-m-d')
        ]);
    }

    public function listAjax($request)
    {
        $searchParams = [];
        if ($request['search_keyword_name']) {
            $searchParams['SearchKeyword'] = $request['search_keyword_name'];
        }
        if ($request['search_begin_date']) {
            $searchParams['SearchBeginDate'] = $request['search_begin_date'];
        }
        if ($request['search_end_date']) {
            $searchParams['SearchEndDate'] = $request['search_end_date'];
        }

        $currentPage = $request['start'];
        $totalPerPage = $request['length'];
        
        Paginator::currentPageResolver(function() use ($currentPage) {
            return $currentPage / 10 + 1;
        });

        $this->executeResultRepository->pushCriteria(new ExecuteResultCriteriaCriteria($searchParams));
        $executeResult = $this->executeResultRepository->orderBy('ExecuteResultIdx', 'DESC')->paginate($totalPerPage);
        
        return response()->json([
            'recordsTotal' => $executeResult['meta']['pagination']['total'],
            'recordsFiltered' => $executeResult['meta']['pagination']['total'],
            'data' => $executeResult['data']
        ]);
    }
}
