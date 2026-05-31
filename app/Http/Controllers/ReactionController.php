<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    // Toggle like/dislike
    public function toggle(Request $request, Post $post)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $user = session('supabase_user');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $type = $request->type; // 'like' atau 'dislike'

        // Cek apakah user sudah pernah reaction ke post ini
        $existing = Reaction::where('post_id', $post->id)
                           ->where('user_id', $user['id'])
                           ->first();

        if ($existing) {
            // Jika klik type yang sama → hapus reaction (unlike)
            if ($existing->type === $type) {
                $existing->delete();
                $newCount = $this->getCounts($post);
                
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'counts' => $newCount
                ]);
            }
            // Jika klik type berbeda → update reaction
            else {
                $existing->update(['type' => $type]);
                $newCount = $this->getCounts($post);
                
                return response()->json([
                    'success' => true,
                    'action' => 'updated',
                    'counts' => $newCount
                ]);
            }
        } 
        // Reaction baru
        else {
            Reaction::create([
                'post_id' => $post->id,
                'user_id' => $user['id'],
                'type' => $type,
            ]);
            
            $newCount = $this->getCounts($post);
            
            return response()->json([
                'success' => true,
                'action' => 'added',
                'counts' => $newCount
            ]);
        }
    }

    // Helper: Hitung like & dislike dari tabel reactions
    private function getCounts(Post $post)
    {
        return [
            'likes' => $post->reactions()->where('type', 'like')->count(),
            'dislikes' => $post->reactions()->where('type', 'dislike')->count(),
        ];
    }
}