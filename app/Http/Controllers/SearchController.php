<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\News;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search posts (Feed section)
     */
    public function feed(Request $request)
    {
        $query = trim(mb_substr($request->get('q', ''), 0, 100));
        
        $posts = Post::with(['user', 'comments'])
            ->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('caption', 'ILIKE', "%{$query}%")
                        ->orWhere('game_title', 'ILIKE', "%{$query}%");
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('feed.index', compact('posts', 'query'));
    }

    /**
     * Search news (News section) - ✅ FIX DI SINI
     */
    public function news(Request $request)
    {
        $query = trim(mb_substr($request->get('q', ''), 0, 100));
        
        // ⚠️ PENTING: Nama variable HARUS $newsList agar match dengan view
        $newsList = News::with('user')
            ->when($query, function($q) use ($query) {
                $q->where(function($sub) use ($query) {
                    $sub->where('caption', 'ILIKE', "%{$query}%")
                        ->orWhere('game_title', 'ILIKE', "%{$query}%");
                });
            })
            ->orderByDesc('score')
            ->paginate(10)
            ->withQueryString();

        // ⚠️ PENTING: compact HARUS pakai 'newsList' (bukan 'news')
        return view('news.index', compact('newsList', 'query'));
    }
}