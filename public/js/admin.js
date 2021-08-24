/*
* DATATABLE DEFAULT SETTING
*/
$.extend( true, $.fn.dataTable.defaults, {
    "autoWidth": false,
    "sPaginationType": "full_numbers",
    "dom": 'T<"clear">lrtip', //<"top"i>rt<"bottom"lp><"clear">
    "aLengthMenu": [[10, 20, 50], [10, 20, 50]],
    "oLanguage": {
        "sSearch": "검색 : ",
        "oPaginate": {
            "sFirst" : "처음",
            "sPrevious" : "이전",
            "sNext" : "다음",
            "sLast" : "마지막"
        },
        "sInfo": "총 _TOTAL_개의 항목 중 _START_ ~ _END_ 표시",
        "sLengthMenu": "_MENU_ 개의 항목 표시",
        "sProcessing": "로드중...",
        "sEmptyTable": "조회 된 데이터가 없습니다.",
        "sInfoEmpty": ""
    },
    columnDefs: [
        {targets: 'no-sort', orderable: false}
    ]
});
