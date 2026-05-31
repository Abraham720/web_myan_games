@extends('layouts.app')

@section('title', 'Buat Post • MyanGames')

@section('content')
<div class="max-w-2xl mx-auto">
    
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('feed') }}" class="text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-2 text-sm mb-4 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Feed
        </a>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">📝 Buat Post Baru</h1>
        <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Bagikan pengalaman gamingmu dengan komunitas</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-card rounded-xl border border-slate-200 dark:border-gray-700 p-6 shadow-xl">
        <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Game Title -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Game Title <span class="text-red-400">*</span></label>
                <input type="text" name="game_title" value="{{ old('game_title') }}" placeholder="Contoh: Genshin Impact, Valorant, Minecraft..."
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition {{ $errors->has('game_title') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                       required maxlength="100">
                @error('game_title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Caption -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Caption <span class="text-red-400">*</span></label>
                <textarea name="caption" rows="4" placeholder="Ceritakan pengalamanmu, tips, atau momen epic..."
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none {{ $errors->has('caption') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                          required maxlength="1000">{{ old('caption') }}</textarea>
                <p class="mt-1 text-xs text-slate-500 dark:text-gray-400 text-right"><span id="charCount">0</span>/1000</p>
                @error('caption')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Gambar (Opsional)</label>
                <div class="border-2 border-dashed border-slate-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary/50 transition cursor-pointer group bg-slate-50 dark:bg-dark/50"
                     onclick="document.getElementById('image_input').click()">
                    <input type="file" name="image" id="image_input" accept="image/*" class="hidden" onchange="previewImage(this)">
                    <div id="upload_placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-400 dark:text-gray-500 group-hover:text-primary transition mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <p class="text-slate-500 dark:text-gray-400 text-sm">Klik untuk upload gambar</p>
                        <p class="text-slate-400 dark:text-gray-500 text-xs mt-1">PNG, JPG, GIF • Max 2MB</p>
                    </div>
                    <img id="image_preview" class="hidden max-h-48 mx-auto rounded-lg object-cover">
                </div>
                @error('image')<p class="mt-2 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-200 dark:border-gray-700">
                <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition shadow-lg shadow-primary/25 flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    Publish Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ✅ FIXED: Properly formatted JavaScript with global scope functions

// 1. Character counter for caption - wrapped in DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.querySelector('textarea[name="caption"]');
    const charCount = document.getElementById('charCount');
    
    if (textarea && charCount) {
        // Update count on input
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        // Initialize count on page load
        charCount.textContent = textarea.value.length;
    }
});

// 2. Image preview function - EXPLICITLY GLOBAL via window object
window.previewImage = function(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const placeholder = document.getElementById('upload_placeholder');
            const preview = document.getElementById('image_preview');
            
            if (placeholder) placeholder.classList.add('hidden');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
        };
        
        // Handle read error
        reader.onerror = function() {
            console.error('Failed to read file:', input.files[0].name);
            alert('Gagal membaca gambar. Silakan coba file lain.');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
};
</script>
@endpush