<?php

namespace Tests\Unit;

use App\User;
use App\Article;
use App\Tag;
use App\UserPrefectureMap;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
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
        $tag1 = Tag::factory()->create();
        $article1 = Article::factory()->create(['user_id' => $user->id]);
        $article1->tags()->attach($tag1);

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

    /**
     * updateメソッドのテスト
     * 
     * @test
     */
    public function testUpdate()
    {
        Storage::fake('s3');

        $user = User::factory()->create();
        $newNickname = 'new nickname';
        $newIntroduction = 'New Introduction';
        $newYoutube = 'new youtube';
        $newTwitter = 'new twitter';
        $newInstagram = 'new instagram';
        $newTiktok = 'new tiktok';

        $this->actingAs($user)->patch(route('users.update', ['name' => $user->name]), [
            'introduction' => $newIntroduction,
            'nickname' => $newNickname,
            'youtube' => $newYoutube,
            'twitter' => $newTwitter,
            'instagram' => $newInstagram,
            'tiktok' => $newTiktok,
            'image' => UploadedFile::fake()->image('profile.jpg'),
            'background_image' => UploadedFile::fake()->image('background.jpg'),
        ]);

        $user->refresh();

        $this->assertEquals($newNickname, $user->nickname);
        $this->assertEquals($newIntroduction, $user->introduction);
        $this->assertEquals($newYoutube, $user->youtube);
        $this->assertEquals($newTwitter, $user->twitter);
        $this->assertEquals($newInstagram, $user->instagram);
        $this->assertEquals($newTiktok, $user->tiktok);
    }

    /**
     * likesメソッドのテスト
     * 
     * @test
     */
    public function testLikes()
    {
        // テスト用のユーザーと記事を作成
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        // いいねを設定
        $user->likes()->attach($article);

        // アクションを実行
        $response = $this->actingAs($user)->get(route('users.likes', ['name' => $user->name]));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.likes');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('articles');
        $response->assertViewHas('tags');
        $response->assertViewHas('record');
        $response->assertViewHas('isFollowing');
    }

    /**
     * conquestメソッドのテスト
     * 
     * @test
     */
    public function testConquest()
    {
        $user = User::factory()->create();

        // テスト用のフォロワーを作成
        $follower = User::factory()->create();
        $user->followers()->attach($follower);

        $response = $this->actingAs($follower)->get(route('users.conquest', ['name' => $user->name]));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.conquest');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('tags');
        $response->assertViewHas('record');
        $response->assertViewHas('isFollowing');
    }
    /**
     * followingsメソッドのテスト
     * 
     * @test
     */
    public function testFollowings()
    {
        $user = User::factory()->create();
        $followingUser = User::factory()->create();
        $user->followings()->attach($followingUser);

        $response = $this->actingAs($user)->get(route('users.followings', ['name' => $user->name]));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.followings');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('followings');
        $response->assertViewHas('tags');
        $response->assertViewHas('record');
        $response->assertViewHas('isFollowing');
    }

    /**
     * followersメソッドのテスト
     * 
     * @test
     */
    public function testFollowers()
    {
        $user = User::factory()->create();
        $followerUser = User::factory()->create();
        $followerUser->followings()->attach($user);

        $response = $this->actingAs($user)->get(route('users.followers', ['name' => $user->name]));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.followers');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('followers');
        $response->assertViewHas('tags');
        $response->assertViewHas('record');
        $response->assertViewHas('isFollowing');
    }
}
