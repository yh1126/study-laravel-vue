<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Comment;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'content' => substr($faker->text, 0, 500),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
