@extends('layouts.app')

@section('title', 'Edit Post • MyanGames')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('feed') }}" class="text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-2 text-sm mb-4 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Feed
        </a>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">✏️ Edit Post</h1>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Perbarui pengalaman gamingmu</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-card rounded-xl border border-slate-200 dark:border-gray-700 p-6 shadow-xl">
        <form action="{{ route('post.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <!-- Game Title -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Game Title <span class="text-red-400">*</span></label>
                <input type="text" name="game_title" value="{{ old('game_title', $post->game_title) }}" 
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition {{ $errors->has('game_title') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                       required maxlength="100">
                @error('game_title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Caption -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Caption <span class="text-red-400">*</span></label>
                <textarea name="caption" rows="4" 
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none {{ $errors->has('caption') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                          required maxlength="1000">{{ old('caption', $post->caption) }}</textarea>
                @error('caption')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Current Image -->
            @if($post->image_url)
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Gambar Saat Ini</label>
                <div class="rounded-lg overflow-hidden bg-slate-100 dark:bg-gray-800 inline-block border border-slate-200 dark:border-gray-700">
                    <img src="{{ $post->image_url }}" alt="Current Image" class="max-h-40 object-cover">
                </div>
            </div>
            @endif

            <!-- Upload New Image -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Ganti Gambar (Opsional)</label>
                <div class="border-2 border-dashed border-slate-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-primary/50 transition cursor-pointer bg-slate-50 dark:bg-dark/50"
                     onclick="document.getElementById('edit_image_input').click()">
                    <input type="file" name="image" id="edit_image_input" accept="image/*" class="hidden" onchange="previewEditImage(this)">
                    <div id="edit_upload_placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-slate-400 dark:text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <p class="text-slate-500 dark:text-gray-400 text-sm">Klik untuk ganti gambar</p>
                        <p class="text-slate-400 dark:text-gray-500 text-xs mt-1">PNG, JPG, GIF • Max 2MB</p>
                    </div>
                    <img id="edit_image_preview" class="hidden max-h-40 mx-auto rounded-lg object-cover">
                </div>
                @error('image')<p class="mt-2 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-200 dark:border-gray-700 flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition shadow-lg shadow-primary/25 hover:scale-[1.02] active:scale-[0.98]">💾 Simpan Perubahan</button>
                <a href="{{ route('feed') }}" class="px-6 py-3 bg-slate-200 dark:bg-gray-700 hover:bg-slate-300 dark:hover:bg-gray-600 text-slate-900 dark:text-white font-medium rounded-lg transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewEditImage(input) { if (input.files && input.files[0]) { const reader = new FileReader(); reader.onload = function(e) { document.getElementById('edit_upload_placeholder').classList.add('hidden'); const preview = document.getElementById('edit_image_preview'); preview.src = e.target.result; preview.classList.remove('hidden'); } reader.readAsDataURL(input.files[0]); } }
</script>
@endpush