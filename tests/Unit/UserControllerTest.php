<?php

namespace Tests\Unit;

use App\User;
use App\Tag;
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
        // レスポンスの確認
        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertViewHas('user', $user);
    }

    /**
     * editメソッドのテスト
     * 
     * @test
     */
    public function testEdit()
    {
        $user = User::factory()->create();

        // 人気タグと関連記事のセットアップ
        // TODO
        // ここで Tag モデルと Article モデル、およびそれらの関連を適切に設定
        // 
        // $tag1 = Tag::factory()->create();
        // $article1 = Article::factory()->create();
        // $article1->tags()->attach($tag1);

        $response = $this->actingAs($user)->get(route('users.edit', ['name' => $user->name]));

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user', function ($viewUser) use ($user) {
            return $viewUser->id === $user->id;
        });

        // 人気タグの検証
        $response->assertViewHas('tags', function ($viewTags) {
            // getPopularTag メソッドの結果とビューに渡されたタグが一致するかを検証
            $popularTags = Tag::getPopularTag();
            return $popularTags->pluck('name')->sort()->values()->all() === $viewTags->pluck('name')->sort()->values()->all();
        });
    }
}
