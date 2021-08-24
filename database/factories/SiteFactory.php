<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\Eduplan\Site;
use Faker\Generator as Faker;

$factory->define(Site::class, function (Faker $faker) {
    return [
        'SiteName' => $faker->word,
        'Color' => hex2bin($faker->numberBetween(100000, 999999)),
        'KeywordSiteType' => $faker->randomElement(['Blog', 'Cafe', 'Post']),
        'IsOwner' => $faker->numberBetween(0, 1),
        'SiteURL' => $faker->url(),
        'RegDatetime' => date('Y-m-d H:i:s'),
        'AdminID' => 'devtest',
        'AdminIdx' => 8
    ];
});
