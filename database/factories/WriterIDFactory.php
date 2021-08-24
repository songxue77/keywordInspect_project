<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\Eduplan\WriterID;
use Faker\Generator as Faker;

$factory->define(WriterID::class, function (Faker $faker) {
    $date = date('Y-m-d H:i:s');
    $writerIds = array_unique($faker->words());

    return [
        'WriterIDCnt' => count($writerIds),
        'RegDatetime' => $date,
        'LastModifyDatetime' => $date,
        'WriterIDData' => json_encode($writerIds)
    ];
});
