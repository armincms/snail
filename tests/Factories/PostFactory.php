<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(\Armincms\Snail\Tests\Fixtures\Post::class, function (Faker $faker) {
    return [ 
        'title' => $faker->word,
        'slug' => Str::slug($faker->word), 
    ];
});
