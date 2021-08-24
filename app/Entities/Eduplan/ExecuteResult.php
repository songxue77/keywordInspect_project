<?php

declare(strict_types=1);

namespace App\Entities\Eduplan;

use Illuminate\Database\Eloquent\Model;

class ExecuteResult extends Model
{
    protected $connection = 'mysql';
    protected $table = 'ExecuteResult';
    protected $primaryKey = 'ExecuteResultIdx';

    protected $fillable = [
        'ExecuteResultIdx',
        'SearchKeyword',
        'KeywordCnt',
        'IsKeywordGroupResult',
        'AdminID',
        'AdminIdx',
        'RegDatetime',
        'ExecuteResult'
    ];

    protected $guarded = [];

    public $timestamps = false;
    const CREATED_AT = 'RegDatetime';
}
