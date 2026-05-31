<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    protected $storage;

    public function __construct(SupabaseStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Display a listing of news (INDEX - WAJIB ADA!)
     */
    public function index()
    {
        // Ambil news terbaru dengan eager loading user & votes
        // Urutkan berdasarkan score DESC (seperti Reddit/HackerNews)
        $newsList = News::with(['user', 'votes'])
            ->orderByDesc('score')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('news.index', compact('newsList'));
    }

    /**
     * Show the form for creating a new news
     */
    public function create()
    {
        $user = session('supabase_user');
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk post news.');
        }
        
        return view('news.create');
    }

    /**
     * Store a newly created news in storage
     */
        // ... (bagian atas tetap sama)

    public function store(Request $request)
    {
        $user = session('supabase_user');
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login untuk post news.');
        }

        $request->validate([
            'game_title' => 'required|string|max:100',
            'caption' => 'required|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            // 1. Upload Image
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->storage->uploadNewsImage($request->file('image'));
            }

            // 2. 🔑 FIX: Pastikan upload berhasil sebelum insert DB
            if (!$imageUrl) {
                return back()->withErrors(['image' => 'Gagal mengupload gambar. Silakan coba lagi.'])->withInput();
            }

            // 3. Insert ke Database
            News::create([
                'user_id' => $user['id'],
                'game_title' => $request->game_title,
                'caption' => $request->caption,
                'image_url' => $imageUrl,
                'upvote_count' => 0,
                'downvote_count' => 0,
                'score' => 0,
            ]);

            return redirect()->route('news')->with('success', 'News berhasil dipublish! 🎉');
            
        } catch (\Exception $e) {
            Log::error('News store error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Gagal menyimpan news: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified news
     */
    public function edit(News $news)
    {
        $user = session('supabase_user');
        
        // Security: Hanya owner yang bisa edit
        if (!$user || $user['id'] !== $news->user_id) {
            abort(403, 'Unauthorized');
        }
        
        return view('news.edit', compact('news'));
    }

    /**
     * Update the specified news in storage
     */
    public function update(Request $request, News $news)
    {
        $user = session('supabase_user');
        
        // Security check
        if (!$user || $user['id'] !== $news->user_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'game_title' => 'required|string|max:100',
            'caption' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $data = [
                'game_title' => $request->game_title,
                'caption' => $request->caption,
            ];

            // Handle new image upload (optional)
            if ($request->hasFile('image')) {
                // Delete old image from Supabase (optional)
                if ($news->image_url) {
                    $this->storage->deleteNewsImage($news->image_url);
                }
                $data['image_url'] = $this->storage->uploadNewsImage($request->file('image'));
            }

            $news->update($data);

            return redirect()->route('news')->with('success', 'News berhasil diupdate! ✨');
            
        } catch (\Exception $e) {
            Log::error('News update error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Gagal mengupdate news.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified news from storage (AJAX)
     */
    public function destroy(News $news)
    {
        $user = session('supabase_user');
        
        // Security: Hanya owner yang bisa hapus
        if (!$user || $user['id'] !== $news->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Delete image from Supabase Storage (optional)
            if ($news->image_url) {
                $this->storage->deleteNewsImage($news->image_url);
            }
            
            // Soft delete via model (karena pakai SoftDeletes)
            $news->delete();
            
            // Return JSON untuk AJAX delete
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('News delete error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus news'], 500);
        }
    }
}