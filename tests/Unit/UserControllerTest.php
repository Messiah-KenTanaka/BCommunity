<?php

namespace Tests\Unit;

use App\User;
use App\UserPrefectureMap;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * showメソッドのテスト
     * 
     * @test
     */
    public function testShow()
    {
        // テスト用のユーザーを作成
        $user = User::factory()->create();

        // ユーザー情報表示のリクエストを送信
        $response = $this->actingAs($user)->get(route('users.show', ['name' => $user->name]));
        dd($response);
        // レスポンスの確認
        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertViewHas('user', $user);
    }
}
