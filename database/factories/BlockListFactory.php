<?php

namespace Database\Factories;

use App\BlockList;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlockListFactory extends Factory
{
    protected $model = BlockList::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'blocked_user_id' => User::factory()
        ];
    }
}
