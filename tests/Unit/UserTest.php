<?php

namespace Tests\Unit;

use App\User;
use App\UserPrefectureMap;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * リレーションのテスト
     *
     * @test
     */
    public function testUserRelations()
    {
        $user = User::factory()->create();
        // dd($user);

        $this->assertInstanceOf(HasMany::class, $user->articles());
        $this->assertInstanceOf(BelongsToMany::class, $user->followers());
        $this->assertInstanceOf(BelongsToMany::class, $user->followings());
        $this->assertInstanceOf(BelongsToMany::class, $user->likes());
        $this->assertInstanceOf(BelongsToMany::class, $user->blockList());
        $this->assertInstanceOf(hasMany::class, $user->article_comments());
        $this->assertInstanceOf(hasMany::class, $user->user_prefecture_maps());
        $this->assertInstanceOf(hasMany::class, $user->retweets());
        $this->assertInstanceOf(hasMany::class, $user->retweets());
    }

    /**
     * フォロワーの数をカウント
     * 
     * @test
     */
    public function testIsFollowedBy()
    {
        $user = User::factory()->create();
        // ダミーのフォロワー二人
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();
        // フォロワーを関連付ける処理
        $user->followers()->attach($follower1->id);
        $user->followers()->attach($follower2->id);

        $this->assertEquals(2, $user->count_followers);
    }

    /**
     * フォロワーの数をカウント
     * 
     * @test
     */
    public function testCountFollowersAttribute()
    {
        $user = User::factory()->create();
        // ダミーのフォロワー二人
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();
        // フォロワーを関連付ける処理
        $user->followers()->attach($follower1->id);
        $user->followers()->attach($follower2->id);

        $this->assertEquals(2, $user->count_followers);
    }

    /**
     * ユーザーの釣果記録都道府県をカウント
     * 
     * @test
     */
    public function testPrefectureCountAttribute()
    {
        $user = User::factory()->create();
        // 関連する都道府県を３つ生成
        UserPrefectureMap::factory()->count(3)->create(['user_id' => $user->id]);
        // カウント数を取得
        $count = $user->prefectureCount;
        $this->assertEquals(3, $count);
    }

    /**
     * ユーザーネームを取得
     * 
     * @test
     */
    public function testUserName()
    {
        $testUserName = 'KenTanaka';
        $user = User::factory()->create(['name' => $testUserName]);
        $this->assertEquals($testUserName, $user->name);
    }

    /**
     * リツイートしたユーザーを取得
     * 
     * @test
     */
    public function testRetweetUsers()
    {
        $user = User::factory()->count(3)->create();
        // リツイートユーザーのIDを配列として準備
        $retweetUserIds = $user->pluck('id')->toArray();
        // リツイートユーザーを取得
        $fetchUsers = User::getRetweetUsers($retweetUserIds);
        // 取得したユーザーの検証
        foreach ($fetchUsers as $fetchUser) {
            $this->assertContains($fetchUser->id, $retweetUserIds);
        }
    }

    /**
     * ユーザー情報を取得
     * 
     * @test
     */
    public function testUser()
    {
        $user = User::factory()->create();
        $userName = User::getUser($user->name);

        $this->assertEquals($userName->name, $user->name);
    }

    /**
     * ユーザー情報、フォロー中のユーザーを情報取得
     * 
     * @test
     */
    public function testUserFollowings()
    {
        // ユーザーとフォロー中のユーザーを作成
        $user = User::factory()->create();
        $followingUsers = User::factory()->count(3)->create();

        // ユーザーが他のユーザーをフォローする
        foreach ($followingUsers as $followingUser) {
            $user->followings()->attach($followingUser);
        }

        // メソッドを呼び出し
        $userFollow = User::getUserFollowings($user->name);

        // フォロー中のユーザーの名前を取得
        $followingUserNames = $userFollow->followings->pluck('name')->toArray();

        // 検証
        foreach ($followingUsers as $followingUser) {
            $this->assertContains($followingUser->name, $followingUserNames);
        }
    }

    /**
     * フォロー中のユーザーを取得
     * 
     * @test
     */
    public function testFollowings()
    {
        // ユーザーとフォロー中のユーザーを作成
        $user = User::factory()->create();
        $followingUsers = User::factory()->count(3)->create();

        // ユーザーが他のユーザーをフォローする
        foreach ($followingUsers as $followingUser) {
            $user->followings()->attach($followingUser);
        }

        // フォロー中のユーザーを取得
        $userFollowings = User::getFollowings($user)->items();

        // フォロー中のユーザーの名前を取得
        $followingUserNames = collect($userFollowings)->pluck('name');

        // 検証
        foreach ($followingUsers as $followingUser) {
            $this->assertContains($followingUser->name, $followingUserNames);
        }
    }

    /**
     * ユーザー情報、フォロワーのユーザーを情報取得
     * 
     * @test
     */
    public function testUserFollowers()
    {
        $user = User::factory()->create();
        $userFollowers = User::factory()->count(3)->create();
        // 他のユーザーがユーザーをフォロー
        foreach ($userFollowers as $userFollower) {
            $userFollower->followers()->attach($user);
        }

        $followerUsers = User::getUserFollowers($user->name);

        $followerUserNames = $followerUsers->pluck('name')->toArray();
        // 検証
        foreach ($userFollowers as $userFollower) {
            $this->assertContains($userFollower->name, $followerUserNames);
        }
    }

    // TODO 後で実装
    /**
     * フォロワーのユーザーを取得
     * 
     * @test
     */
    // public function testFollowers()
    // {
    //     $user = User::factory()->create();
    //     $userFollowers = User::factory()->count(3)->create();

    //     // 他のユーザーがユーザーをフォロー
    //     foreach ($userFollowers as $userFollower) {
    //         $userFollower->followers()->attach($user);
    //     }

    //     $followerUsers = User::getFollowers($user)->items();
    //     // dd($followerUsers); // デバッグ出力を削除またはコメントアウト

    //     $followerUserIds = collect($followerUsers)->pluck('name');

    //     foreach ($userFollowers as $userFollower) {
    //         $this->assertContains($userFollower->name, $followerUserIds);
    //     }
    // }

    /**
     * ブロック中のユーザーを取得
     * 
     * @test
     */
    public function testBlockUserList()
    {
        $user = User::factory()->create();
        $blockedUsers = User::factory()->count(3)->create();

        // ユーザーが他のユーザーをブロック
        foreach ($blockedUsers as $blockedUser) {
            $user->blockList()->attach($blockedUser);
        }

        // ブロックリストを取得
        $blockUserList = User::getBlockUserList($user);

        // ブロックリストに含まれるユーザーIDを取得
        $blockedUserIds = $blockUserList->pluck('id')->toArray();

        // 検証
        foreach ($blockedUsers as $blockedUser) {
            $this->assertContains($blockedUser->id, $blockedUserIds);
        }
    }
}
