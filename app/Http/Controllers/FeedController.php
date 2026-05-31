<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index()
    {
        // Ambil post terbaru dengan eager loading relasi user & comments.user
        // Urutkan dari yang terbaru (created_at DESC)
        $posts = Post::with(['user', 'comments.user'])
                     ->latest('created_at')
                     ->paginate(10);

        return view('feed.index', compact('posts'));
    }
}