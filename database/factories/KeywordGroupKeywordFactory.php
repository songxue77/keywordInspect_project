<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\Eduplan\KeywordGroupKeyword;
use Faker\Generator as Faker;

$factory->define(KeywordGroupKeyword::class, function (Faker $faker, $params) {
    return [
        'KeywordGroupIdx' => $params['KeywordGroupIdx'],
        'Keyword' => $params['Keyword']
    ];
});
