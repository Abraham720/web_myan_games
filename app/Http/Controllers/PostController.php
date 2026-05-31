<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $storage;

    public function __construct(SupabaseStorage $storage)
    {
        $this->storage = $storage;
    }

    public function create()
    {
        if (!session()->has('supabase_user')) {
            return redirect()->route('login')->with('error', 'Silakan login untuk membuat post.');
        }
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // ... (kode store yang sudah ada, tidak diubah) ...
        $validator = Validator::make($request->all(), [
            'game_title' => 'required|string|max:100',
            'caption'    => 'required|string|max:1000',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $currentUser = session('supabase_user');
        if (!$currentUser || !isset($currentUser['id'])) {
            return redirect()->route('login')->with('error', 'Session expired. Silakan login ulang.');
        }
        $userId = $currentUser['id'];

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->storage->upload('news-images', $request->file('image'));
            if (!$imageUrl) {
                return back()->with('error', 'Gagal mengupload gambar. Coba lagi.');
            }
        }

        try {
            Post::create([
                'user_id'    => $userId,
                'game_title' => $request->game_title,
                'caption'    => $request->caption,
                'image_url'  => $imageUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create post', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menyimpan post. Silakan coba lagi.');
        }

        return redirect()->route('feed')->with('success', 'Post berhasil dibuat! 🎮');
    }

    // ✅ EDIT: Tampilkan form edit post
    public function edit(Post $post)
    {
        $currentUser = session('supabase_user');
        
        // Authorization: Hanya pemilik post yang boleh edit
        if (!$currentUser || $currentUser['id'] !== $post->user_id) {
            abort(403, 'Unauthorized: Anda tidak berhak mengedit post ini.');
        }

        return view('posts.edit', compact('post'));
    }

    // ✅ UPDATE: Simpan perubahan post
    public function update(Request $request, Post $post)
    {
        $currentUser = session('supabase_user');
        
        // Authorization check
        if (!$currentUser || $currentUser['id'] !== $post->user_id) {
            abort(403, 'Unauthorized');
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'game_title' => 'required|string|max:100',
            'caption'    => 'required|string|max:1000',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $imageUrl = $post->image_url; // Default: pakai gambar lama

        // Jika ada gambar baru, upload dan ganti
        if ($request->hasFile('image')) {
            $newUrl = $this->storage->upload('news-images', $request->file('image'));
            if ($newUrl) {
                $imageUrl = $newUrl;
                // Opsional: hapus gambar lama dari Supabase Storage
            }
        }

        // Update database
        $post->update([
            'game_title' => $request->game_title,
            'caption'    => $request->caption,
            'image_url'  => $imageUrl,
        ]);

        return redirect()->route('feed')->with('success', 'Post berhasil diperbarui! ✨');
    }

    // ✅ DELETE: Soft delete post (hanya pemilik)
    public function destroy(Post $post)
    {
        $currentUser = session('supabase_user');
        
        // Authorization check
        if (!$currentUser || $currentUser['id'] !== $post->user_id) {
            abort(403, 'Unauthorized');
        }

        // Soft delete: set deleted_at, data tidak hilang dari DB
        $post->delete();

        // Jika request dari AJAX, return JSON
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('feed')->with('success', 'Post berhasil dihapus. 🗑️');
    }
}