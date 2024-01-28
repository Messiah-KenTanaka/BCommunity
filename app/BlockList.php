<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlockList extends Model
{
    use HasFactory;

    protected $table = 'block_list';

    protected $fillable = [
        'user_id',
        'blocked_user_id'
    ];

    // ブロックリスト取得
    public static function getBlockList($userId)
    {
        return self::where('user_id', $userId)->pluck('blocked_user_id');
    }

    // すでにブロック済みか確認
    public static function isBlockUser($userId, $blockedUserId)
    {
        return self::where('user_id', $userId)
            ->where('blocked_user_id', $blockedUserId)
            ->exists();
    }
}
