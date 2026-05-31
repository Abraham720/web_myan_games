<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    // Store comment
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // Ambil user UUID dari session Supabase
        $user = session('supabase_user');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $comment = Comment::create([
                'post_id' => $post->id,      // UUID post
                'user_id' => $user['id'],    // UUID user dari Supabase
                'content' => $request->content,
            ]);

            // Return JSON untuk AJAX (bisa juga redirect jika mau full page)
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at?->diffForHumans(),
                    'user' => [
                        'username' => $user['username'] ?? 'User',
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Comment create error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan komentar'], 500);
        }
    }

    // Delete comment (hanya pemilik yang bisa hapus)
    public function destroy(Comment $comment)
    {
        $user = session('supabase_user');
        if (!$user || $user['id'] !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Soft delete
        $comment->delete();
        
        return response()->json(['success' => true]);
    }
}