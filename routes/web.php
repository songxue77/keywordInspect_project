<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'middleware' => ['auth']
], function () {
    Route::get('', 'HomeController@index');
    Route::get('home', 'HomeController@index');

    Route::group([
        'namespace' => 'Eduplan',
        'prefix' => 'eduplan',
        'as' => 'eduplan::',
    ], function () {
        // 키워드 조회
        Route::get('execute', 'ExecuteResultController@index')->name('execute.index');
        Route::get('execute/show/{executeResultIdx}', 'ExecuteResultController@show')->name('execute.show');
        Route::get('execute/process', 'ExecuteResultController@process')->name('execute.crawling.process');
        Route::get('execute/processByKeywordName/{keyword}', 'ExecuteResultController@processSingle')->name('execute.crawling.process.single');
        Route::get('execute/statistics', 'ExecuteResultController@statistics')->name('execute.statistics');
        Route::get('execute/excelOutput', 'ExecuteResultController@excelExport')->name('execute.excel.output');
        Route::get('execute/list', 'ExecuteResultController@list')->name('execute.list');
        Route::get('execute/listAjax', 'ExecuteResultController@listAjax')->name('execute.list.ajax');
    });
});
