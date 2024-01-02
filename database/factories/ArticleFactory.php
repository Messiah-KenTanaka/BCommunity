<?php

namespace Database\Factories;

use App\Article;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(1, 1000),
            'title' => $this->faker->realText(20),
            'body' => $this->faker->realText,
            'user_id' => function () {
                return User::factory()->create()->id;
            }, // 関連するUserモデルを生成し、そのIDを割り当てる
        ];
    }
}
