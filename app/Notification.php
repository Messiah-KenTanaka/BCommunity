<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'article_id',
        'article_comment_id',
        'type',
        'read',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function article_comment()
    {
        return $this->belongsTo(ArticleComment::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // 特定のユーザーの全ての未読通知を取得
    public static function getUnreadNotifications()
    {
        return self::where('receiver_id', auth()->id())
            ->where('read', false)
            ->get();
    }
}
