<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // ✅ SHOW: Manual fetch by UUID string
    public function show($profile)  // ← Parameter biasa, bukan 'Profile $profile'
    {
        // Fetch manual menggunakan UUID
        $profileModel = Profile::find($profile);
        
        if (!$profileModel) {
            Log::warning('Profile not found: ' . $profile);
            abort(404, 'Profil tidak ditemukan.');
        }

        // Load data dengan pagination
        $posts = $profileModel->posts()
                            ->with(['user', 'comments', 'reactions'])
                            ->latest('created_at')
                            ->paginate(5);
                            
        $news = $profileModel->news()
                           ->with('user')
                           ->orderByDesc('score')
                           ->paginate(5);

        // Load counts untuk stats
        $profileModel->loadCount(['posts', 'news']);

        return view('profile.show', compact('profileModel', 'posts', 'news'));
        // ⚠️ Perhatikan: compact('profileModel') bukan 'profile' agar tidak bentrok dengan param
    }

    // ✅ EDIT: Manual fetch + auth check
    public function edit($profile)
    {
        $profileModel = Profile::find($profile);
        if (!$profileModel) abort(404);

        $currentUser = session('supabase_user');
        
        if (!$currentUser || $currentUser['id'] !== $profileModel->id) {
            abort(403, 'Unauthorized');
        }

        return view('profile.edit', compact('profileModel'));
    }

    // ✅ UPDATE: Manual fetch + auth check + validation
    public function update(Request $request, $profile)
    {
        $profileModel = Profile::find($profile);
        if (!$profileModel) abort(404);

        $currentUser = session('supabase_user');
        
        if (!$currentUser || $currentUser['id'] !== $profileModel->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/',
        ], [
            'username.regex' => 'Username hanya boleh huruf, angka, underscore.',
        ]);

        try {
            $profileModel->update(['username' => $request->username]);

            // Update session jika username berubah
            if ($currentUser['username'] !== $request->username) {
                $currentUser['username'] = $request->username;
                session(['supabase_user' => $currentUser]);
            }

            return redirect()->route('profile', $profileModel->id)
                           ->with('success', 'Profil berhasil diperbarui! ✨');
                           
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui profil.');
        }
    }
}