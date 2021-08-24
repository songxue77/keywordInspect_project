<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\Admin;
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

$factory->define(Admin::class, function (Faker $faker) {
    $datetime = date('Y-m-d H:i:s');

    return [
        'LoginID' => $faker->firstName,
        'Password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'AdminName' => $faker->lastName,
        'RegDatetime' => $datetime,
        'DeleteDatetime' => '9999-12-31 00:00:00',
        'LastExecuteDatetime' => $datetime,
        'LastPasswordModifyDatetime' => $datetime
    ];
});
