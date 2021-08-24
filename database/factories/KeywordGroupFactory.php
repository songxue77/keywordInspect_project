<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\Eduplan\KeywordGroup;
use Faker\Generator as Faker;

$factory->define(KeywordGroup::class, function (Faker $faker) {
    $date = date('Y-m-d H:i:s');

    return [
        'KeywordGroupName' => $faker->name,
        'KeywordCnt' => random_int(1, 10),
        'RegDatetime' => $date,
        'LastModifyDatetime' => $date,
        'IsUse' => random_int(0, 1),
        'AdminID' => $faker->name,
        'AdminIdx' => $faker->randomDigit
    ];
});
