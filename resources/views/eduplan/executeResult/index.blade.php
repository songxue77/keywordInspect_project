@extends('adminlte::page')

@section('title', '키워드 조회')

@section('content_header')
@stop

@section('content')
    <div class="col-md-12 row">
        <div class="col-md-12 btn-group btn-group-toggle">
            <label class="btn bg-info location-index-area active">
                <input type="radio" name="options" id="location_index" autocomplete="off" checked="">신규 키워드 조회
            </label>
            <label class="btn bg-info location-history-area">
                <input type="radio" name="options" id="location_history" autocomplete="off">이전 조회 결과
            </label>
        </div>
        
        <div class="col-md-12 process-mode-only-area" style="margin-top: 10px;">
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary" id="writer_id_modal">등록된 아이디</button>
                <button type="button" class="btn btn-primary" id="site_modal">등록된 카페</button>
            </div>
        </div>

        <div class="col-md-12 process-mode-only-area" style="margin-top: 10px;">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        조회 키워드 조건 설정
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <button type="button" class="btn btn-secondary" id="keyword_group_modal">키워드 불러오기</button>
                            <button type="button" class="btn btn-secondary" id="keyword_group_process">View 검색 (전체)</button>
                        </div>
                        <div class="col-2">
                            <input type="text" class="form-control" id="keyword_name" name="keyword_name" placeholder="개별 조회 키워드 입력">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-secondary" id="keyword_single_process">View 검색 (개별)</button>
                        </div>
                        <div class="col-2">
                        </div>
                        <div class="col-2 text-right">
                            <button type="button" class="btn btn-success execute-result-export" style="display: none;">엑셀출력</button>
                            <button type="button" class="btn btn-danger execute-result-reset">검색 초기화 (Reset)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12 view-mode-only-area" style="margin-top: 10px; display: none;">
            <div class="card card-default card-outline">
                <div class="card-body">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="text-center" style="background-color: #e9ecef; font: bold;">조회 키워드</td>
                            <td class="text-center">{{$executeSearchKeyword}}</td>
                            <td class="text-center" style="background-color: #e9ecef; font: bold;">조회일</td>
                            <td class="text-center">{{$executeResult['data']['RegDatetime'] ?? '-'}}</td>
                            <td class="text-center" style="background-color: #e9ecef; font: bold;">조회키워드 수</td>
                            <td class="text-center">{{$executeKeywordCount}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="card card-success collapsed-card" id="card_main">
                <div class="card-header">
                    <h3 class="card-title">지정 채널 점유비율</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-caret-down"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-1 input-group">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-block">채널지정</button>
                                </span>
                            </div>
                            <div class="col-md-2 input-group input-group-flat">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-flat" style="background-color: #31859C; color: white;">자사</button>
                                </div>
                                <input type="text" class="form-control" id="is_own_cafe_name" name="is_own_cafe_name" value="{{$executeResultArray['StatisticsResult']['isOwn']['cafeName'] ?? ''}}">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-primary btn-flat" id="is_own_statistics" >조회</button>
                                </span>
                            </div>
                            <div class="col-md-2 input-group input-group-flat">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-flat" style="background-color: #953735; color: white;">타사</button>
                                </div>
                                <input type="text" class="form-control" id="is_not_own_cafe_name" name="is_not_own_cafe_name" value="{{$executeResultArray['StatisticsResult']['isNotOwn']['cafeName'] ?? ''}}">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-primary btn-flat" id="is_not_own_statistics">조회</button>
                                </span>
                            </div>
                            <div class="col-md-5">
                            </div>
                            <div class="col-md-2 text-right">
                                <span style="font: bold;">*표시단위 : 개수(비율)</span>
                            </div>
                        </div>
                        
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td class="text-center inspect-td">검수날짜</td>
                                    <td colspan="7" class="text-center is-owner-td-top">자사 키워드 노출력 점검</td>
                                    <td colspan="7" class="text-center is-not-owner-td-top">타사 키워드 노출력 점검</td>
                                    <td colspan="2" class="text-center total-td-top">자사VS타사 노출비교</td>
                                </tr>
                                <tr>
                                    <td class="text-center inspect-date">{{$executeResult['data']['RegDatetime'] ?? '-'}}</td>
                                    <td rowspan="2" class="text-center is-owner-td">VIEW 1위<br>노출 수</td>
                                    <td rowspan="2" class="text-center is-owner-td">카페 1위<br>노출 수</td>
                                    <td colspan="4" class="text-center is-owner-td">VIEW 전체 상위권 노출 수 (1~5위 노출)</td>
                                    <td rowspan="2" class="text-center is-owner-td">VIEW_카페<br>상위 노출수</td>
                                    <td rowspan="2" class="text-center is-not-owner-td">VIEW 1위<br>노출 수</td>
                                    <td rowspan="2" class="text-center is-not-owner-td">카페 1위<br>노출 수</td>
                                    <td colspan="4" class="text-center is-not-owner-td">VIEW 전체 상위권 노출 수 (1~5위 노출)</td>
                                    <td rowspan="2" class="text-center is-not-owner-td">VIEW_카페<br>상위 노출수</td>
                                    <td rowspan="2" class="text-center total-td">VIEW섹션<br>1위 노출</td>
                                    <td rowspan="2" class="text-center total-td">VIEW섹션<br>상위권 노출</td>
                                </tr>
                                <tr>
                                    <td class="text-center inspect-td">검수 키워드 수</td>
                                    <td class="text-center category-is-own-td">View 전체</td>
                                    <td class="text-center category-is-own-td">카페</td>
                                    <td class="text-center category-is-own-td">포스트</td>
                                    <td class="text-center category-is-own-td">블로그</td>
                                    <td class="text-center category-is-not-own-td">View 전체</td>
                                    <td class="text-center category-is-not-own-td">카페</td>
                                    <td class="text-center category-is-not-own-td">포스트</td>
                                    <td class="text-center category-is-not-own-td">블로그</td>
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
                    </div>
                </div>
            </div>
        </div>
        
        <form class="" id="search_form" name="search_form">
            <input type="hidden" id="mode" name="mode" value="{{$mode}}">
            <input type="hidden" id="keyword_group_idx" name="keyword_group_idx" value="">
            <input type="hidden" id="keyword_count" name="keyword_count" value="{{$executeKeywordCount}}">
            <input type="hidden" id="execute_result_idx" name="execute_result_idx" value="{{$executeResultIdx}}">
        </form>
        
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title process-mode-only-area">총 <span id="keywords_cnt" style="text-decoration: underline;">{{$executeKeywordCount}}</span>개의 키워드 조회</h3>
                    
                    <div class="text-right process-mode-only-area">
                        ▶ 점검일시 : <span id="inspect_datetime">{{$executeResultIdx ? $executeResult['data']['RegDatetime'] : '-'}}</span>
                    </div>
                    <div class="text-right view-mode-only-area" style="display: none;">
                        <button type="button" class="btn btn-success execute-result-export">엑셀출력</button>
                        <button type="button" class="btn btn-info location-list">목록으로</button>
                    </div>
                </div>
                <div class="card-body">
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
                </div>
            </div>
        </div>
    </div>

    @includeIf('eduplan.executeResult.template.keyword_group_modal')
    @includeIf('eduplan.executeResult.template.writer_id_modal')
    @includeIf('eduplan.executeResult.template.site_modal')
@endsection

@section('css')
    <style>
        .inspect-td {background-color: #7F7F7F; color: white;}
        .is-owner-td-top {background-color: #31859C; color: white;}
        .is-owner-td {background-color: #93CDDD;}
        .is-not-owner-td-top {background-color: #953735; color: white;}
        .is-not-owner-td {background-color: #D99694}
        .total-td-top {background-color: #376092; color: white;}
        .total-td {background-color: #B9CDE5}
        .category-is-own-td {background-color: #DBEEF4}
        .category-is-not-own-td {background-color: #F2DCDB}
    </style>
@stop

@section('js')
    <script type="text/javascript">
        // 키워드 그룹 선택 모달창
        var keyword_group_table_layer_column_idx;
        var keyword_group_datatable;
        
        // 등록된 아이디 모달창
        var writer_id_table_layer_column_idx;
        var writer_id_datatable;
        
        // 등록된 사이트
        var site_table_layer_column_idx;
        var site_datatable;

        // 타이머
        var timerInterval;
        
        // 뷰 모드
        var mode = $('#mode').val();
        if (mode === 'view') {
            $('.process-mode-only-area').hide();
            $('.view-mode-only-area').show();
            $('#is_own_cafe_name').attr('readonly', true);
            $('#is_own_statistics').attr('disabled', true);
            $('#is_not_own_cafe_name').attr('readonly', true);
            $('#is_not_own_statistics').attr('disabled', true);
            $('.location-index-area').removeClass('active');
            $('.location-history-area').addClass('active');
            $('#card_main').removeClass('collapsed-card');
        }
        
        $(document).ready(function() {
            if ($('#execute_result_idx')) {
                $('.execute-result-export').show();
            }
            
            $('.location-list').click(function() {
                location.href = '{{route('eduplan::execute.list')}}';
            });
            
            $('#location_history').click(function() {
                location.href = '{{route('eduplan::execute.list')}}';
            });
            
            $('.execute-result-reset').click(function() {
                location.href = '{{route('eduplan::execute.index')}}';
            });
            
            $('.execute-result-export').click(function() {
                location.href = '{{route('eduplan::execute.excel.output')}}?executeResultIdx='+$('#execute_result_idx').val();
            });
            
            $('#keyword_group_modal').click(function() {
                var executeResultIdx = $('#execute_result_idx').val();
                if (executeResultIdx) {
                    Swal.fire('검색 초기화 후 다시 조회해주세요.');
                    return false;
                }
                
                $('#keywordGroupModalTemplate').modal('show');
            });
            
            $('#writer_id_modal').click(function() {
                $('#writerIdModalTemplate').modal('show');
            });
            
            $('#site_modal').click(function() {
                $('#siteModalTemplate').modal('show');
            })
            
            // 키워드 그룹 dataTable에 검색Input 추가
            $('#keyword_group_table_layer tr.search th').each( function () {
                var title = $(this).text();
                if (title != '') {
                    $(this).html( '<input type="text" placeholder="'+title+'" class="form-control"/>' );
                }
            });
            
            // 키워드 그룹 datatable 초기화
            keyword_group_datatable = $('#keyword_group_table_layer').DataTable({
                ajax: false,
                ordering: false,
                lengthChange: false,
                bInfo: false,
                oLanguage: {
                    "sEmptyTable": "설정된 키워드그룹이 없습니다.", // 전체 결과
                    "sZeroRecords": "검색된 키워드그룹이 없습니다." // filter 결과
                },
                columnDefs: [
                    {"targets": keyword_group_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": keyword_group_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": keyword_group_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": keyword_group_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": keyword_group_table_layer_column_idx++, "orderable": false, "sClass":"text-center"}
                ]
            });

            // 키워드 그룹 dataTable 검색기능
            keyword_group_datatable.columns().every(function () {
                var that = this;

                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });

            // 등록된 아이디 dataTable에 검색Input 추가
            $('#writer_id_table_layer tr.search th').each( function () {
                var title = $(this).text();
                if (title != '') {
                    $(this).html( '<input type="text" placeholder="'+title+'" class="form-control"/>' );
                }
            });

            // 등록된 아이디 datatable 초기화
            writer_id_datatable = $('#writer_id_table_layer').DataTable({
                ajax: false,
                ordering: false,
                lengthChange: false,
                bInfo: false,
                bPaginate: false,
                oLanguage: {
                    "sEmptyTable": "등록된 아이디가 없습니다.", // 전체 결과
                    "sZeroRecords": "검색된 아이디가 없습니다." // filter 결과
                },
                columnDefs: [
                    {"targets": writer_id_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": writer_id_table_layer_column_idx++, "orderable": false, "sClass":"text-center"}
                ]
            });

            // 등록된 아이디 dataTable 검색기능
            writer_id_datatable.columns().every(function () {
                var that = this;

                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });

            // 등록된 카페 dataTable에 검색Input 추가
            $('#site_table_layer tr.search th').each( function () {
                var title = $(this).text();
                if (title != '') {
                    $(this).html( '<input type="text" placeholder="'+title+'" class="form-control"/>' );
                }
            });

            // 등록된 카페 datatable 초기화
            site_datatable = $('#site_table_layer').DataTable({
                ajax: false,
                ordering: false,
                lengthChange: false,
                bInfo: false,
                bPaginate: false,
                oLanguage: {
                    "sEmptyTable": "등록된 카페가 없습니다.", // 전체 결과
                    "sZeroRecords": "검색된 카페가 없습니다." // filter 결과
                },
                columnDefs: [
                    {"targets": site_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": site_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": site_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": site_table_layer_column_idx++, "orderable": false, "sClass":"text-center"},
                    {"targets": site_table_layer_column_idx++, "orderable": false, "sClass":"text-center"}
                ]
            });

            // 등록된 카페 dataTable 검색기능
            site_datatable.columns().every(function () {
                var that = this;

                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
            
            $('#select_keyword_group').click(function() {
                var selectedKeywordGroupIdx = $('input[name=keyword_group_idx_layer]:checked').val();
                if (selectedKeywordGroupIdx === undefined) {
                    Swal.fire('키워드 그룹을 선택해주세요.');
                    return false;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route('eduplan::group.keywrod.list.ajax')}}',
                    dataType: 'json',
                    method: 'GET',
                    async: false,
                    data: {
                        keywordGroupIdx: selectedKeywordGroupIdx
                    },
                    success: function (response) {
                        if (response.code === '0000') {
                            addKeywordsToTable(response.keywordGroupKeywords);
                            $('#keywords_cnt').text(response.keywordGroupKeywordsCount);
                            $('#keyword_count').val(response.keywordGroupKeywordsCount);
                            $('#keyword_group_idx').val(selectedKeywordGroupIdx);
                            $('#keywordGroupModalTemplate').modal('toggle');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '오류 발생..',
                                text: response.message
                            });
                        }
                    },
                    error: function (response) {
                        Swal.fire({
                            icon: 'error',
                            title: '오류 발생..',
                            text: response.message
                        });
                    }
                })
            });
            
            $('#keyword_group_process').click(function() {
                var executeResultIdx = $('#execute_result_idx').val();
                if (executeResultIdx) {
                    Swal.fire('검색 초기화 후 다시 클릭해주세요.');
                    return false;
                }
                
                var keywordGroupIdx = $('#keyword_group_idx').val();
                if (keywordGroupIdx === '') {
                    Swal.fire('키워드 그룹을 선택해주세요.');
                    return false;
                }
                
                var keywordCount = $('#keyword_count').val();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route('eduplan::execute.crawling.process')}}',
                    dataType: 'json',
                    method: 'GET',
                    cache: false,
                    data: {
                        keywordGroupIdx: keywordGroupIdx
                    },
                    beforeSend: function () {
                        var ua = window.navigator.userAgent;
                        var msie = ua.indexOf("MSIE ");
                        
                        if ((msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) && keywordCount > 100) {
                            var processTime = keywordCount * 1.1;
                            var processDate = new Date();
                            var hour = processDate.getHours()
                            hour = hour >= 10 ? hour : '0' + hour
                            var min = processDate.getMinutes()
                            var sec = processDate.getSeconds()
                            sec = sec >= 10 ? sec : '0' + sec
                            var processTimeFormat = hour + ':' + min + ':' + sec;
                            
                            Swal.fire({
                                title: '키워드 검수 시작',
                                text: 'IE브라우저로 100개 이상의 키워드 검수 시 정확한 검수완료시간 확인이 불가하여 '+processTime+'초 ' +
                                    '후에 리스트페이지에서 새로 고침하여 조회시간: '+processTimeFormat+' ' +
                                    '최종편집자가 본인 이름으로 되어있는 조회결과로 확인해주세요.',
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: '리스트 페이지 이동'
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    location.href = '{{route('eduplan::execute.list')}}';
                                }
                            })
                        } else {
                            Swal.fire({
                                title: '키워드 조회 중..',
                                html: '<b></b> 초 남았습니다.',
                                timer: keywordCount * 1.1 * 1000,
                                timerProgressBar: true,
                                didOpen: function () {
                                    Swal.showLoading();
                                    timerInterval = setInterval(function() {
                                        const content = Swal.getHtmlContainer()
                                        if (content) {
                                            const b = content.querySelector('b');
                                            if (b) {
                                                b.textContent = Math.floor(Swal.getTimerLeft() / 1000)
                                            }
                                        }
                                    }, 100)
                                },
                                willClose: function () {
                                    clearInterval(timerInterval);
                                }
                            }).then(function (result) {
                                clearInterval(timerInterval);
                            });
                        }
                    },
                    success: function (response) {
                        if (response.code === '0000') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            
                            location.href = '{{route('eduplan::execute.show', '')}}/'+response.data.executeResultIdx;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '오류 발생..',
                                text: response.message
                            });
                        }
                    },
                    error: function (response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'IE 브라우저 오류 발생..',
                            text: response.message
                        });
                    }
                })
            });
            
            $('#keyword_single_process').click(function() {
                var executeResultIdx = $('#execute_result_idx').val();
                if (executeResultIdx) {
                    Swal.fire('검색 초기화 후 다시 클릭해주세요.');
                    return false;
                }

                var keywordName = $('#keyword_name').val();
                if (keywordName === '') {
                    Swal.fire('개별 키워드 이름을 입력해주세요.');
                    return false;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route('eduplan::execute.crawling.process')}}',
                    dataType: 'json',
                    method: 'GET',
                    data: {
                        keywordName: keywordName
                    },
                    beforeSend: function () {
                        Swal.fire({
                            title: '키워드 조회 중..',
                            html: '<b></b> 초 남았습니다.',
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: function () {
                                Swal.showLoading();
                                timerInterval = setInterval(function() {
                                    const content = Swal.getHtmlContainer()
                                    if (content) {
                                        const b = content.querySelector('b');
                                        if (b) {
                                            b.textContent = Math.floor(Swal.getTimerLeft() / 1000)
                                        }
                                    }
                                }, 100)
                            },
                            willClose: function () {
                                clearInterval(timerInterval);
                            }
                        }).then(function(result) {
                            clearInterval(timerInterval);
                        });
                    },
                    success: function (response) {
                        if (response.code === '0000') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });

                            location.href = '{{route('eduplan::execute.show', '')}}/'+response.data.executeResultIdx;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '오류 발생..',
                                text: response.message
                            });
                        }
                    },
                    error: function (response) {
                        Swal.fire({
                            icon: 'error',
                            title: '오류 발생..',
                            text: response.message
                        });
                    }
                })
            });
            
            $('#is_own_statistics').click(function() {
                var cafeName = $('#is_own_cafe_name').val();
                if (!cafeName) {
                    Swal.fire('자사 카페명을 입력해주세요.');
                    return false;
                }
                
                var executeResultIdx = $('#execute_result_idx').val();
                if (!executeResultIdx) {
                    Swal.fire('키워드 검수 후 조회 가능합니다.');
                    return false;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route('eduplan::execute.statistics')}}',
                    dataType: 'json',
                    method: 'GET',
                    async: false,
                    data: {
                        cafeName: cafeName,
                        executeResultIdx: executeResultIdx,
                        isOwn: 1
                    },
                    success: function (response) {
                        if (response.code === '0000') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 500
                            });
                            
                            $('.is-owner-view-top-rank-count').text(response.data.statistics.viewTopRankCountText);
                            $('.is-owner-cafe-top-rank-count').text(response.data.statistics.cafeTopRankCountText);
                            $('.is-owner-view-all-count').text(response.data.statistics.viewTotalCountText);
                            $('.is-owner-view-cafe-count').text(response.data.statistics.viewTotalCafeCountText);
                            $('.is-owner-view-post-count').text(response.data.statistics.viewTotalPostCountText);
                            $('.is-owner-view-blog-count').text(response.data.statistics.viewTotalBlogCountText);
                            $('.is-owner-cafe-all-count').text(response.data.statistics.cafeTotalCountText);
                            $('.compare-view-top-rank').text(response.data.compare.viewTopRankCompareText);
                            $('.compare-view-total').text(response.data.compare.viewTotalCompareText);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '오류 발생..',
                                text: response.message
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    }
                })
            });

            $('#is_not_own_statistics').click(function() {
                var cafeName = $('#is_not_own_cafe_name').val();
                if (!cafeName) {
                    Swal.fire('타사 카페명을 입력해주세요.');
                    return false;
                }

                var executeResultIdx = $('#execute_result_idx').val();
                if (!executeResultIdx) {
                    Swal.fire('키워드 검수 후 조회 가능합니다.');
                    return false;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{route('eduplan::execute.statistics')}}',
                    dataType: 'json',
                    method: 'GET',
                    async: false,
                    data: {
                        cafeName: cafeName,
                        executeResultIdx: executeResultIdx,
                        isOwn: 0
                    },
                    success: function (response) {
                        if (response.code === '0000') {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 500
                            });

                            $('.is-not-owner-view-top-rank-count').text(response.data.statistics.viewTopRankCountText);
                            $('.is-not-owner-cafe-top-rank-count').text(response.data.statistics.cafeTopRankCountText);
                            $('.is-not-owner-view-all-count').text(response.data.statistics.viewTotalCountText);
                            $('.is-not-owner-view-cafe-count').text(response.data.statistics.viewTotalCafeCountText);
                            $('.is-not-owner-view-post-count').text(response.data.statistics.viewTotalPostCountText);
                            $('.is-not-owner-view-blog-count').text(response.data.statistics.viewTotalBlogCountText);
                            $('.is-not-owner-cafe-all-count').text(response.data.statistics.cafeTotalCountText);
                            $('.compare-view-top-rank').text(response.data.compare.viewTopRankCompareText);
                            $('.compare-view-total').text(response.data.compare.viewTotalCompareText);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '오류 발생..',
                                text: response.message
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response);
                    }
                })
            });
        });
        
        function addKeywordsToTable(keywordGroupKeywords)
        {
            var html = '';
            keywordGroupKeywords.forEach(function(element) {
                html += '<tr>';
                    html += '<td class="text-center">' + element + '</td>';
                    html += '<td colspan="27"></td>'
                html += '</tr>';
            });

            $('.no-keyword-row').remove();
            $('#keyword_group_keywords_body').append(html);
        }
    </script>
@stop
