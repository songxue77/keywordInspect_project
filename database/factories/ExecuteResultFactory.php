<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\Eduplan\ExecuteResult;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(ExecuteResult::class, function (Faker $faker) {
    $datetime = date('Y-m-d H:i:s');
    $searchKeyword = $faker->word;

    return [
        'SearchKeyword' => $searchKeyword,
        'KeywordCnt' => 1,
        'IsKeywordGroupResult' => 0,
        'AdminID' => $faker->lastName,
        'AdminIdx' => $faker->randomDigit,
        'RegDatetime' => $datetime,
        'ExecuteResult' => json_encode([
            'ProcessResult' => [
                0 => [
                    'KeywordGroupIdx' => 0,
                    'inspectDatetime' => $datetime,
                    'code' => '0000',
                    'message' => '조회성공',
                    'result' => [
                        'keyword' => $searchKeyword,
                        'monthlyPcQcCnt' => 10000,
                        'monthlyMobileQcCnt' => 12000,
                        'monthlyTotalQcCnt' => 22000,
                        'aHeadSiteType' => '공통',
                        'sectionRank' => '2',
                        'cafeTopRank' => 0,
                        'isAdShowTop' => false,
                        'siteLink' => [
                            [
                                'Name' => $faker->word,
                                'Color' => $faker->hexColor,
                                'FontColor' => $faker->colorName,
                                'WriterId' => $faker->firstName,
                                'SiteType' => $faker->randomElement(['Blog', 'Cafe', 'Post'])
                            ],
                            [
                                'Name' => $faker->word,
                                'Color' => $faker->hexColor,
                                'FontColor' => $faker->colorName,
                                'WriterId' => $faker->firstName,
                                'SiteType' => $faker->randomElement(['Blog', 'Cafe', 'Post'])
                            ],
                            [
                                'Name' => $faker->word,
                                'Color' => $faker->hexColor,
                                'FontColor' => $faker->colorName,
                                'WriterId' => $faker->firstName,
                                'SiteType' => $faker->randomElement(['Blog', 'Cafe', 'Post'])
                            ],
                            [
                                'Name' => $faker->word,
                                'Color' => '',
                                'FontColor' => 'black',
                                'WriterId' => '',
                                'SiteType' => $faker->randomElement(['Blog', 'Cafe', 'Post'])
                            ],
                            [
                                'Name' => $faker->word,
                                'Color' => '',
                                'FontColor' => 'black',
                                'WriterId' => '',
                                'SiteType' => $faker->randomElement(['Blog', 'Cafe', 'Post'])
                            ]
                        ],
                        'cafeLink' => [
                            [
                                'Name' => '기타(C)',
                                'Color' => '',
                                'FontColor' => 'black',
                                'WriterId' => '',
                                'SiteType' => 'Cafe'
                            ],
                            [
                                'Name' => $faker->word,
                                'Color' => $faker->hexColor,
                                'FontColor' => 'white',
                                'WriterId' => $faker->firstName,
                                'SiteType' => 'Cafe'
                            ],
                            [
                                'Name' => '기타(C)',
                                'Color' => '',
                                'FontColor' => 'black',
                                'WriterId' => '',
                                'SiteType' => 'Cafe'
                            ],
                            [
                                'Name' => $faker->word,
                                'Color' => $faker->hexColor,
                                'FontColor' => 'white',
                                'WriterId' => $faker->firstName,
                                'SiteType' => 'Cafe'
                            ],
                            [
                                'Name' => '기타(C)',
                                'Color' => '',
                                'FontColor' => 'black',
                                'WriterId' => '',
                                'SiteType' => 'Cafe'
                            ]
                        ]
                    ]
                ]
            ]
        ])
    ];
});
