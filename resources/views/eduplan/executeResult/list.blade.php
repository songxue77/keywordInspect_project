@extends('adminlte::page')

@section('title', '키워드 조회')

@section('content_header')
@stop

@section('content')
    <div class="col-md-12 row">
        <div class="col-md-12 btn-group btn-group-toggle">
            <label class="btn bg-info location-index-area">
                <input type="radio" name="options" id="location_index" autocomplete="off" checked="">신규 키워드 조회
            </label>
            <label class="btn bg-info location-history-area active">
                <input type="radio" name="options" id="location_history" autocomplete="off">이전 조회 결과
            </label>
        </div>
        
        <div class="col-md-12">
            <h5 class="mt-4 mb-2">이전 키워드 조회 결과</h5>
        </div>
        
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="card card-default card-outline">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-1 text-right">
                            키워드명 :
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="search_keyword_name" name="search_keyword_name">
                        </div>
                        <div class="col-md-1 text-right">
                            조회일 :
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <div class="input-group date" id="search_begin_date_area" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="search_begin_date" data-target="#search_begin_date_area" value="{{$defaultBeginDate}}">
                                    <div class="input-group-append" data-target="#search_begin_date_area" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <div class="input-group date" id="search_end_date_area" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="search_end_date" data-target="#search_end_date_area" value="{{$defaultEndDate}}">
                                    <div class="input-group-append" data-target="#search_end_date_area" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="submit" class="btn btn-primary btn-lrg ajax" id="search_button">
                                검 색
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="card card-default card-outline">
                <div class="card-body">
                    <table class="table table-bordered" id="execute_result_table">
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th>키워드명</th>
                                <th>조회 키워드 수</th>
                                <th>조회단위</th>
                                <th>조회일시</th>
                                <th>최종편집자</th>
                                <th>상세보기</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    
@stop

@section('js')
    <script type="text/javascript">
        
        var execute_result_datatable;
        var column_idx = 0;
        
        $(document).ready(function() {
            $('.date').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            $('#location_index').click(function() {
                location.href = '{{route('eduplan::execute.index')}}';
            });

            // 키워드 그룹 datatable 초기화
            execute_result_datatable = $('#execute_result_table').DataTable({
                aLengthMenu: [[10, 20, 30], [10, 20, 30]],
                bInfo: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url : '{{route('eduplan::execute.list.ajax')}}',
                    type : 'GET',
                    headers : {
                        'X-CSRF-TOKEN' : '{{csrf_token()}}'
                    },
                    data : function(data) {
                        data.search_begin_date = $('#search_begin_date').val();
                        data.search_end_date = $('#search_end_date').val();
                        data.search_keyword_name = $('#search_keyword_name').val();
                    }
                },
                oLanguage: {
                    "sEmptyTable": "설정된 조회결과가 없습니다.", // 전체 결과
                    "sZeroRecords": "검색된 조회결과가 없습니다." // filter 결과
                },
                columnDefs: [
                    {'data': 'ExecuteResultIdx', 'render': function(data, type, row, meta) {
                        return execute_result_datatable.page.info().recordsTotal - (meta.row + meta.settings._iDisplayStart);
                    }, 'targets': column_idx++, 'orderable': false, 'sClass': 'text-center', 'width': '5%'},
                    {'data': 'SearchKeyword', 'orderable': false, 'render': function(data, type, full, meta) {
                        var fontColor = full.IsKeywordGroupResult === 1 ? '#0070C0' : '#ED6C0A';

                        return '<a href="/eduplan/execute/show/'+full.ExecuteResultIdx+'?mode=view" style="color: '+fontColor+'">'+data+'</a>';
                    }, 'targets': column_idx++, 'sClass':'text-center', 'width':'30%'},
                    {'data': 'KeywordCnt', 'targets': column_idx++, 'orderable': false, 'sClass':'text-center', 'width':'10%'},
                    {'data': 'SearchResultIdx', 'orderable': false, 'render': function(data, type, full, meta) {
                        var text = full.IsKeywordGroupResult === 1 ? '그룹' : '개별';
                        var fontColor = full.IsKeywordGroupResult === 1 ? '#0070C0' : '#ED6C0A';
                        
                        return '<span style="color: '+fontColor+';">'+text+'</span>';
                    }, 'targets': column_idx++, 'sClass':'text-center', 'width':'10%'},
                    {'data': 'RegDatetime', 'targets': column_idx++, 'orderable': false, 'sClass':'text-center', 'width':'15%'},
                    {'data': 'AdminID', 'targets': column_idx++, 'orderable': false, 'sClass':'text-center', 'width':'15%'},
                    {'data': 'ExecuteResultIdx', 'orderable': false, 'render': function(data, type, full, meta) {
                        return '<a href="/eduplan/execute/show/'+full.ExecuteResultIdx+'?mode=view" class="btn btn-info btn-xs">상세보기<i class="fa fa-lg fa-fw fa-pen"></i></>';
                    }, 'targets': column_idx++, 'sClass':'text-center', 'width':'15%'}
                ]
            });
            
            $('#search_button').click(function(event) {
                execute_result_datatable.draw();
            });
        });
        
    </script>
@stop