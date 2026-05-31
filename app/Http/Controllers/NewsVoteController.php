<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsVote;
use Illuminate\Http\Request;

class NewsVoteController extends Controller
{
    // Toggle upvote/downvote dengan AJAX
    public function toggle(Request $request, News $news)
    {
        $request->validate([
            'type' => 'required|in:upvote,downvote',
        ]);

        $user = session('supabase_user');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $type = $request->type; // 'upvote' atau 'downvote'

        // Cek apakah user sudah pernah vote ke news ini
        $existing = NewsVote::where('news_id', $news->id)
                           ->where('user_id', $user['id'])
                           ->first();

        if ($existing) {
            // Jika klik type yang sama → hapus vote (undo)
            if ($existing->type === $type) {
                $existing->delete();
                $news->updateScoreCounters(); // Update counter di DB
                
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'score' => $news->score,
                    'upvotes' => $news->upvote_count,
                    'downvotes' => $news->downvote_count,
                ]);
            }
            // Jika klik type berbeda → update vote
            else {
                $existing->update(['type' => $type]);
                $news->updateScoreCounters();
                
                return response()->json([
                    'success' => true,
                    'action' => 'updated',
                    'score' => $news->score,
                    'upvotes' => $news->upvote_count,
                    'downvotes' => $news->downvote_count,
                ]);
            }
        } 
        // Vote baru
        else {
            NewsVote::create([
                'news_id' => $news->id,
                'user_id' => $user['id'],
                'type' => $type,
            ]);
            
            $news->updateScoreCounters();
            
            return response()->json([
                'success' => true,
                'action' => 'added',
                'score' => $news->score,
                'upvotes' => $news->upvote_count,
                'downvotes' => $news->downvote_count,
            ]);
        }
    }
}