<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\BlockList;
use App\ArticleComment;
use App\Follow;
use App\Retweet;
use App\Notification;
use App\UserPrefectureMap;
use App\Http\Requests\ArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Functions;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->authorizeResource(Article::class, 'article');
        $this->articleService = $articleService;
    }

    public function index()
    {
        $userId = auth()->id(); // ログインユーザーのIDを取得

        // 現在のページ数を取得
        $currentPage = request()->get('page', 1);

        // ページ1の場合のみリツイートされた記事を取得
        if ($currentPage == 1) {
            // フォローしているユーザーIDを取得
            $followingUsers = Follow::getFollow($userId);

            $recentRetweets = Retweet::getRetweet($followingUsers);

            // リツイートされた記事を取得
            $retweetArticles = $this->articleService->getRetweetArticles($recentRetweets);
        } else {
            $retweetArticles = collect();  // 空のコレクションを作成
        }

        // ブロックリストからブロックしたユーザーのIDを取得
        $blockUsers = BlockList::getBlockList($userId);

        // 投稿記事一覧を取得
        $articles = $this->articleService->getArticleIndex($blockUsers);

        // 各ユーザーに対して称号のパス取得処理を行う
        $articles->transform(function ($item) {
            $item->user->achievementImage = Functions::getAchievementTitle($item->user->prefecture_count);

            return $item;
        });


        $tags = Tag::getPopularTag();

        return view('articles.index', [
            'retweetArticles' => $retweetArticles,
            'articles' => $articles,
            'tags' => $tags,
        ]);
    }

    public function create()
    {
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $tags = Tag::getPopularTag();

        $bassField = config('bassField');

        return view('articles.create', [
            'allTagNames' => $allTagNames,
            'tags' => $tags,
            'bassField' => $bassField,
        ]);
    }

    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;

        // S3画像アップロード
        $file = $request->file('image');
        if (isset($file) && !empty($file->getPathname())) {
            // S3に画像を保存する
            $file = Functions::ImageUploadResize($file);
            $path = Storage::disk('s3')->putFile('bcommunity_img', $file, 'public');
            $article->image = Storage::disk('s3')->url($path);
        }
        // S3画像アップロード2
        $file2 = $request->file('image2');
        if (isset($file2) && !empty($file2->getPathname())) {
            // S3に画像を保存する
            $file2 = Functions::ImageUploadResize($file2);
            $path = Storage::disk('s3')->putFile('bcommunity_img', $file2, 'public');
            $article->image2 = Storage::disk('s3')->url($path);
        }
        // S3画像アップロード3
        $file3 = $request->file('image3');
        if (isset($file3) && !empty($file3->getPathname())) {
            // S3に画像を保存する
            $file3 = Functions::ImageUploadResize($file3);
            $path = Storage::disk('s3')->putFile('bcommunity_img', $file3, 'public');
            $article->image3 = Storage::disk('s3')->url($path);
        }

        $article->save();

        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        // 全国制覇MAPテーブルに追加
        if (
            $request->pref != 'その他' && !empty($request->pref) && !empty($request->image) &&
            (!empty($request->fish_size) || !empty($request->weight))
        ) {
            $userPrefectureMap = UserPrefectureMap::firstOrCreate([
                'user_id' => $request->user()->id,
                'prefecture' => $request->pref,
            ]);
        }

        return redirect()->route('articles.index')
            ->with('success', '投稿しました。');
    }


    public function edit(Article $article)
    {
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $tags = Tag::getPopularTag();

        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
            'tags' => $tags,
        ]);
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();

        $article->tags()->detach();
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    public function delete(Article $article)
    {
        $article->publish_flag = 0;
        $article->save();

        return redirect()->route('articles.index')
            ->with('success', '投稿を削除しました。');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article, $notificationId = null)
    {
        // 通知を既読にする
        if ($notificationId) {
            $notification = Notification::find($notificationId);
            if ($notification && $notification->receiver_id == auth()->id()) {
                // 特定の記事に関連するすべての通知を取得
                $relatedNotifications = Notification::where('article_id', $notification->article_id)
                    ->where('receiver_id', auth()->id())
                    ->get();

                // すべての関連通知を既読に更新
                foreach ($relatedNotifications as $relatedNotification) {
                    $relatedNotification->read = true;
                    $relatedNotification->save();
                }
            }
        }

        // 記事が削除されていればリダイレクト
        if ($article->publish_flag == 0) {
            return redirect()->back()->with('success', '選択した投稿は削除されており表示できません。');
        }

        $comments = $article->article_comments()->with('user')
            ->where('publish_flag', 1)
            ->orderByDesc('created_at')
            ->paginate(config('paginate.paginate_50'));

        $article->comment_count = $comments->count();

        // ユーザーの称号パス取得処理を行う
        $article->user->achievementImage = Functions::getAchievementTitle($article->user->prefecture_count);

        $tags = Tag::getPopularTag();

        return view('articles.show', [
            'article' => $article,
            'tags' => $tags,
            // 'comments' => $comments,
        ]);
    }

    public function getComments($article_id)
    {
        try {
            $comments = $this->articleService->getArticleComment($article_id);

            return response()->json([
                'status' => 1,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json([
                'error_flag' => 1,
                'message' => 'コメントを取得に失敗しました。'
            ]);
        }
    }

    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function retweet(Request $request, Article $article)
    {
        $article->retweets()->detach($request->user()->id);
        $article->retweets()->attach($request->user()->id);

        // 自分自身の投稿をリツイートした場合、通知を作成しない
        if ($request->user()->id != $article->user_id) {
            // 通知を作成
            Notification::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $article->user_id,
                'article_id' => $article->id,
                'type' => 'retweet',
                'read' => false,  // 未読
            ]);
        }

        return [
            'id' => $article->id,
            'countRetweets' => $article->count_retweets,
        ];
    }

    public function unRetweet(Request $request, Article $article)
    {
        $article->retweets()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countRetweets' => $article->count_retweets,
        ];
    }

    public function comment(Request $request, ArticleComment $article_comment)
    {
        $article_comment->fill($request->all());

        $article_comment->save();

        return redirect()->route('articles.index')
            ->with('success', 'コメントしました。');
    }
}
