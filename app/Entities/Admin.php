<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $connection = 'mysql';
    protected $table = 'Admin';
    protected $primaryKey = 'AdminIdx';

    protected $fillable = [
        'LoginID',
        'Password',
        'AdminName',
        'RegDatetime',
        'DeleteDatetime',
        'LastExecuteDatetime',
        'LastPasswordModifyDatetime'
    ];

    protected $guarded = [];

    public $timestamps = false;
    const CREATED_AT = 'RegDatetime';

    public function getAuthPassword()
    {
        return trim($this->Password);
    }
}
