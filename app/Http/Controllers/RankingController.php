<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Tag;

class RankingController extends Controller
{
    public function index()
    {
        // 全国ランキング
        $ranking = Article::with(['user', 'likes', 'tags'])
            ->whereNotNull('image')
            ->whereNotNull('fish_size')
            ->orderByDesc('fish_size')
            ->limit(50)
            ->get();

            $tags = Tag::getPopularTag();

        return view('ranking.index', [
            'ranking' => $ranking,
            'tags' => $tags,
        ]);
    }

    public function show(string $pref)
    {
        // 都道府県ランキング
        $ranking = Article::with(['user', 'likes', 'tags'])
            ->where('pref', $pref)
            ->whereNotNull('image')
            ->whereNotNull('fish_size')
            ->orderByDesc('fish_size')
            ->limit(30)
            ->get(); 

            $tags = Tag::getPopularTag();

        return view('ranking.show', [
            'ranking' => $ranking,
            'tags' => $tags,
            'pref' => $pref,
        ]);
    }
}
