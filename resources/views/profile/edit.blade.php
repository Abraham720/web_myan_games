@extends('layouts.app')

@section('title', 'Edit Profil • MyanGames')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('profile', $profileModel->id) }}" class="text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-2 text-sm mb-4 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Profil
        </a>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">✏️ Edit Profil</h1>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Perbarui informasi publik kamu</p>
    </div>

    <div class="bg-white dark:bg-card rounded-2xl border border-slate-200 dark:border-gray-700 p-6 shadow-xl">
        <form action="{{ route('profile.update', $profileModel->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <!-- Avatar Preview -->
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-2xl font-bold text-white shadow-lg">
                    {{ strtoupper(substr($profileModel->username ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-slate-900 dark:text-white font-medium">Avatar</p>
                    <p class="text-slate-500 dark:text-gray-400 text-sm">Otomatis dari inisial username</p>
                </div>
            </div>

            <!-- Username Input -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Username <span class="text-red-400">*</span></label>
                <input type="text" name="username" value="{{ old('username', $profileModel->username) }}" placeholder="Contoh: ProGamer123"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition {{ $errors->has('username') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                       required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+">
                <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Hanya huruf, angka, dan underscore. Minimal 3 karakter.</p>
                @error('username')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Info: Email tidak bisa diubah -->
            <div class="p-4 bg-slate-100 dark:bg-gray-800/50 rounded-lg border border-slate-200 dark:border-gray-700">
                <p class="text-sm text-slate-600 dark:text-gray-400"><span class="font-medium text-slate-700 dark:text-gray-300">Catatan:</span><br>Email dan password dikelola oleh Supabase Auth. Untuk mengubahnya, gunakan fitur "Forgot Password" atau kontak support.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-gray-700">
                <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition shadow-lg shadow-primary/25">💾 Simpan Perubahan</button>
                <a href="{{ route('profile', $profileModel->id) }}" class="px-6 py-3 bg-slate-200 dark:bg-gray-700 hover:bg-slate-300 dark:hover:bg-gray-600 text-slate-900 dark:text-white font-medium rounded-lg transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection