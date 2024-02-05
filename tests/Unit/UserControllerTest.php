<?php

namespace Tests\Unit;

use App\User;
use App\Article;
use App\Tag;
use App\Notification;
use App\BlockList;
use App\UserPrefectureMap;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
    /**
     * blockのテストケース
     * 
     * @test
     */
    public function testBlock()
    {
        $user = User::factory()->create();
        $blockedUser = User::factory()->create();
        $user->blockList()->attach($blockedUser);

        $response = $this->actingAs($user)->get(route('users.block', ['name' => $user->name]));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.block');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('blockList');
        $response->assertViewHas('tags');
        $response->assertViewHas('record');
        $response->assertViewHas('isFollowing');
    }

    /**
     * followのテストケース
     * 
     * @test
     */
    public function testFollow()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)->put(route('users.follow', ['name' => $otherUser->name]));

        // フォロー処理のアサーション
        $this->assertTrue($user->followings->contains($otherUser));

        // 通知のアサーション
        $this->assertDatabaseHas('notifications', [
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'type' => 'follow',
            'read' => false
        ]);

        // レスポンスのアサーション
        $response->assertOk();
        $response->assertJson(['name' => $otherUser->name]);
    }

    /**
     * userBlockのテストケース
     * 
     * @test
     */
    public function testUserBlock()
    {
        $user = User::factory()->create();
        $blockedUser = User::factory()->create();

        $response = $this->actingAs($user)->post(route('users.userBlock', ['userId' => $user->id]), [
            'article_user_id' => $blockedUser->id
        ]);

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success', 'ユーザーをブロックしました。');
        $this->assertDatabaseHas('block_list', [
            'user_id' => $user->id,
            'blocked_user_id' => $blockedUser->id
        ]);
    }

    /**
     * searchUsersメソッドのテストケース
     * 
     * @test
     */
    public function testSearchUsers()
    {
        // テスト用のユーザーを作成
        $user = User::factory()->create(['nickname' => 'testnickname']);
        $searchedNickname = 'testnickname';

        $response = $this->get(route('searchUsers', ['nickname' => $searchedNickname]));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.search_users');
        $response->assertViewHas('users', function ($viewUsers) use ($user) {
            return $viewUsers->contains($user);
        });
        $response->assertViewHas('tags');
        $response->assertViewHas('searched_name', $searchedNickname);
    }

    /**
     * notificationsメソッドのテストケース
     * 
     * @test
     */
    public function testNotifications()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // テスト用の通知を作成
        Notification::create([
            'sender_id' => $otherUser->id,
            'receiver_id' => $user->id,
            'type' => 'test',
            'read' => false,
        ]);

        $response = $this->actingAs($user)->get(route('notifications'));

        // アサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.notifications');
        $response->assertViewHas('notifications', function ($viewNotifications) use ($user) {
            return $viewNotifications->first()->receiver_id === $user->id;
        });
        $response->assertViewHas('tags');
    }

    /**
     * confirmDeleteUserメソッドのテストケース
     * 
     * @test
     */
    public function testConfirmDeleteUser()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('users.confirmDeleteUser', ['userId' => $user->id]));

        // 正しいアクセスのアサーション
        $response->assertStatus(200);
        $response->assertViewIs('users.confirm_delete_user');
        $response->assertViewHas('user', $user);
        $response->assertViewHas('tags');
    }

    /**
     * deleteUserメソッドのテストケース
     * 
     * @test
     */
    public function testDeleteUser()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword')
        ]);

        $response = $this->actingAs($user)->delete(route('users.deleteUser', ['userId' => $user->id]), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'correctpassword'
        ]);

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success', 'ユーザーを削除しました。');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertFalse(Auth::check());
    }
}
