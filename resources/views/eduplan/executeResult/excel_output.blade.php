<table class="table table-bordered table-sm">
    <tbody>
        <tr>
            <td class="text-center" style="background-color: #7F7F7F; color: white;">검수날짜</td>
            <td colspan="7" class="text-center" style="background-color: #31859C; color: white;">자사 키워드 노출력 점검</td>
            <td colspan="7" class="text-center" style="background-color: #953735; color: white;">타사 키워드 노출력 점검</td>
            <td colspan="2" class="text-center" style="background-color: #376092; color: white;">자사VS타사 노출비교</td>
        </tr>
        <tr>
            <td class="text-center inspect-date">{{$executeResult['data']['RegDatetime'] ?? '-'}}</td>
            <td rowspan="2" class="text-center" style="background-color: #93CDDD;">VIEW 1위<br>노출 수</td>
            <td rowspan="2" class="text-center" style="background-color: #93CDDD;">카페 1위<br>노출 수</td>
            <td colspan="4" class="text-center" style="background-color: #93CDDD;">VIEW 전체 상위권 노출 수 (1~5위 노출)</td>
            <td rowspan="2" class="text-center" style="background-color: #93CDDD;">VIEW_카페<br>상위 노출수</td>
            <td rowspan="2" class="text-center" style="background-color: #D99694;">VIEW 1위<br>노출 수</td>
            <td rowspan="2" class="text-center" style="background-color: #D99694;">카페 1위<br>노출 수</td>
            <td colspan="4" class="text-center" style="background-color: #D99694;">VIEW 전체 상위권 노출 수 (1~5위 노출)</td>
            <td rowspan="2" class="text-center" style="background-color: #D99694;">VIEW_카페<br>상위 노출수</td>
            <td rowspan="2" class="text-center" style="background-color: #B9CDE5;">VIEW섹션<br>1위 노출</td>
            <td rowspan="2" class="text-center" style="background-color: #B9CDE5;">VIEW섹션<br>상위권 노출</td>
        </tr>
        <tr>
            <td class="text-center" style="background-color: #7F7F7F; color: white;">검수 키워드 수</td>
            <td class="text-center" style="background-color: #DBEEF4;">View 전체</td>
            <td class="text-center" style="background-color: #DBEEF4;">카페</td>
            <td class="text-center" style="background-color: #DBEEF4;">포스트</td>
            <td class="text-center" style="background-color: #DBEEF4;">블로그</td>
            <td class="text-center" style="background-color: #F2DCDB;">View 전체</td>
            <td class="text-center" style="background-color: #F2DCDB;">카페</td>
            <td class="text-center" style="background-color: #F2DCDB;">포스트</td>
            <td class="text-center" style="background-color: #F2DCDB;">블로그</td>
        </tr>
        <tr>
            <td class="text-center inspect-keyword-cnt">{{$executeKeywordCount}}</td>
            <td class="text-center is-owner-view-top-rank-count">{{$executeResultArray['StatisticsResult']['isOwn']['viewTopRankCountText'] ?? ''}}</td>
            <td class="text-center is-owner-cafe-top-rank-count">{{$executeResultArray['StatisticsResult']['isOwn']['cafeTopRankCountText'] ?? ''}}</td>
            <td class="text-center is-owner-view-all-count">{{$executeResultArray['StatisticsResult']['isOwn']['viewTotalCountText'] ?? ''}}</td>
            <td class="text-center is-owner-view-cafe-count">{{$executeResultArray['StatisticsResult']['isOwn']['viewTotalCafeCountText'] ?? ''}}</td>
            <td class="text-center is-owner-view-post-count">{{$executeResultArray['StatisticsResult']['isOwn']['viewTotalPostCountText'] ?? ''}}</td>
            <td class="text-center is-owner-view-blog-count">{{$executeResultArray['StatisticsResult']['isOwn']['viewTotalBlogCountText'] ?? ''}}</td>
            <td class="text-center is-owner-cafe-all-count">{{$executeResultArray['StatisticsResult']['isOwn']['cafeTotalCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-view-top-rank-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['viewTopRankCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-cafe-top-rank-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['cafeTopRankCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-view-all-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['viewTotalCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-view-cafe-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['viewTotalCafeCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-view-post-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['viewTotalPostCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-view-blog-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['viewTotalBlogCountText'] ?? ''}}</td>
            <td class="text-center is-not-owner-cafe-all-count">{{$executeResultArray['StatisticsResult']['isNotOwn']['cafeTotalCountText'] ?? ''}}</td>
            <td class="text-center compare-view-top-rank">{{$executeResultArray['StatisticsResult']['compare']['viewTopRankCompareText'] ?? ''}}</td>
            <td class="text-center compare-view-total">{{$executeResultArray['StatisticsResult']['compare']['viewTotalCompareText'] ?? ''}}</td>
        </tr>
    </tbody>
</table>

<table class="table table-bordered table-sm">
    <thead class="thead-light">
        <tr>
            <th rowspan="2" class="text-center">키워드</th>
            <th colspan="3" class="text-center">월간 조회수</th>
            <th rowspan="2" class="text-center">강세</th>
            <th rowspan="2" class="text-center">섹션<br>순위</th>
            <th colspan="6" class="text-center">VIEW_전체 노출순위</th>
            <th colspan="5" class="text-center">VIEW_카페 노출순위</th>
            <th rowspan="2" class="text-center">상단광고<br>노출여부</th>
        </tr>
        <tr>
            <th class="text-center">PC</th>
            <th class="text-center">MO</th>
            <th class="text-center">PC+MO</th>
            <th class="text-center">1위</th>
            <th class="text-center">2위</th>
            <th class="text-center">3위</th>
            <th class="text-center">4위</th>
            <th class="text-center">5위</th>
            <th class="text-center">최상위카페<br>키워드순위</th>
            <th class="text-center">1위</th>
            <th class="text-center">2위</th>
            <th class="text-center">3위</th>
            <th class="text-center">4위</th>
            <th class="text-center">5위</th>
        </tr>
    </thead>
    <tbody id="keyword_group_keywords_body">
        @if($executeResultIdx)
            @foreach($executeResultArray['ProcessResult'] as $executeResultData)
                <tr>
                    <td class="text-center"><p>{{$executeResultData['result']['keyword']}}</p></td>
                    <td class="text-center">{{number_format($executeResultData['result']['monthlyPcQcCnt'])}}</td>
                    <td class="text-center">{{number_format($executeResultData['result']['monthlyMobileQcCnt'])}}</td>
                    <td class="text-center">{{number_format($executeResultData['result']['monthlyTotalQcCnt'])}}</td>
                    <td class="text-center">{{$executeResultData['result']['aHeadSiteType']}}</td>
                    <td class="text-center">{{$executeResultData['result']['sectionRank']}}</td>
                    @if(count($executeResultData['result']['siteLink']) > 0)
                        @foreach($executeResultData['result']['siteLink'] as $siteLink)
                            @if($siteLink['WriterId'])
                                <td class="text-center" style="background-color: {{'#'.$siteLink['Color']}}; color: {{$siteLink['FontColor']}};">{{$siteLink['Name']}}<br>({{$siteLink['WriterId']}})</td>
                            @else
                                <td class="text-center" style="background-color: {{'#'.$siteLink['Color']}}; color: {{$siteLink['FontColor']}};">{{$siteLink['Name']}}</td>
                            @endif
                        @endforeach
                    @endif
                    <td class="text-center" style="background-color: #e6e1e1;">{{$executeResultData['result']['cafeTopRank'] === 0 ? '미노출' : $executeResultData['result']['cafeTopRank']}}</td>
                    @if(count($executeResultData['result']['cafeLink']) > 0)
                        @foreach($executeResultData['result']['cafeLink'] as $cafeLink)
                            @if($cafeLink['WriterId'])
                                <td class="text-center" style="background-color: {{'#'.$cafeLink['Color']}}; color: {{$cafeLink['FontColor']}};">{{$cafeLink['Name']}}<br>({{$cafeLink['WriterId']}})</td>
                            @else
                                <td class="text-center" style="background-color: {{'#'.$cafeLink['Color']}}; color: {{$cafeLink['FontColor']}};">{{$cafeLink['Name']}}</td>
                            @endif
                        @endforeach
                    @endif
                    <td class="text-center" style="background-color: #e6e1e1;">{{$executeResultData['result']['isAdShowTop'] === true ? 'O' : 'X'}}</td>
                </tr>
            @endforeach
        @else
            <tr class="no-keyword-row">
                <td colspan="18" class="text-center">입력한 키워드가 없습니다.</td>
            </tr>
        @endif
    </tbody>
</table>