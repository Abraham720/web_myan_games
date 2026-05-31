@extends('layouts.app')

@section('title', 'News • MyanGames')

@section('content')
<div class="space-y-6">
    
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">🗞️ Game News</h1>
            <p class="text-slate-500 dark:text-gray-400 text-sm mt-1">Berita terbaru dari komunitas gamer</p>
        </div>
        
        @php $currentUser = session('supabase_user'); @endphp
        @if($currentUser)
        <a href="{{ route('news.create') }}" 
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-lg shadow-primary/25 hover:scale-105 active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Post News
        </a>
        @endif
    </div>

    <!-- Search Bar -->
    <x-search-bar action="search.news" placeholder="Cari berita game..." :initialQuery="$query ?? ''" />

    <!-- Search Result Badge -->
    @if(isset($query) && $query)
    <div class="mb-4 p-3 bg-primary/10 dark:bg-primary/20 border border-primary/30 rounded-lg flex items-center justify-between">
        <p class="text-sm text-slate-700 dark:text-gray-300">
            Menampilkan hasil untuk: <span class="font-semibold text-primary">"{{ $query }}"</span>
        </p>
        <a href="{{ route('news') }}" class="text-xs text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition">Reset pencarian</a>
    </div>
    @endif
    
    <!-- News List -->
    @forelse ($newsList as $item)
        <article class="bg-white dark:bg-card rounded-xl border border-slate-200 dark:border-gray-700 overflow-hidden hover:border-primary/50 hover:shadow-glow-lg hover:-translate-y-1 transition-all duration-300 group animate-fade-in" id="news-{{ $item->id }}">
            
            <!-- News Header -->
            <div class="p-4 flex items-center gap-3 border-b border-slate-200 dark:border-gray-700/50">
                <div class="w-10 h-10 bg-gradient-to-br from-accent to-primary rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg animate-float">
                    {{ strtoupper(substr($item->user->username ?? 'N', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 dark:text-white truncate">{{ $item->user->username ?? 'Anonymous' }}</p>
                    <p class="text-xs text-slate-500 dark:text-gray-400">{{ $item->created_at?->diffForHumans() }}</p>
                </div>
                <span class="px-3 py-1 bg-gradient-to-r from-primary/20 to-accent/20 border border-primary/30 rounded-full text-xs font-bold text-accent">Score: {{ $item->score }}</span>
            </div>

            <!-- News Image -->
            @if($item->image_url)
                <div class="relative">
                    <img src="{{ $item->image_url }}" alt="{{ $item->game_title }}" class="w-full h-48 md:h-64 object-cover hover:scale-105 transition duration-300" onerror="this.parentElement.style.display='none'">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                        <h3 class="text-lg font-bold text-white">{{ $item->game_title }}</h3>
                    </div>
                </div>
            @endif

            <!-- News Content -->
            <div class="p-4">
                <p class="text-slate-700 dark:text-gray-300 leading-relaxed">{{ $item->caption }}</p>
            </div>

            <!-- Vote Actions -->
            <div class="px-4 py-3 border-t border-slate-200 dark:border-gray-700/50 flex items-center gap-4 text-slate-500 dark:text-gray-400">
                <button onclick="toggleVote('{{ $item->id }}', 'upvote')" class="flex items-center gap-2 hover:text-green-500 dark:hover:text-green-400 transition group/btn {{ $item->votes->contains('type', 'upvote') ? 'text-green-500 dark:text-green-400' : '' }}" id="upvote-btn-{{ $item->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover/btn:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                    <span class="text-sm font-medium" id="upvote-count-{{ $item->id }}">{{ $item->upvote_count }}</span>
                </button>
                <button onclick="toggleVote('{{ $item->id }}', 'downvote')" class="flex items-center gap-2 hover:text-red-500 dark:hover:text-red-400 transition group/btn {{ $item->votes->contains('type', 'downvote') ? 'text-red-500 dark:text-red-400' : '' }}" id="downvote-btn-{{ $item->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover/btn:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    <span class="text-sm font-medium" id="downvote-count-{{ $item->id }}">{{ $item->downvote_count }}</span>
                </button>

                @if($currentUser && $currentUser['id'] === $item->user_id)
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('news.edit', $item->id) }}" class="text-slate-400 dark:text-gray-500 hover:text-primary transition p-1" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                    <button onclick="deleteNews('{{ $item->id }}')" class="text-slate-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition p-1" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>
                @endif
            </div>
        </article>
    @empty
        <div class="text-center py-16 bg-white dark:bg-card rounded-xl border border-slate-200 dark:border-gray-700 border-dashed">
            <div class="text-6xl mb-4">🗞️</div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">Belum ada berita</h3>
            <p class="text-slate-500 dark:text-gray-400 mb-6">Jadilah yang pertama membagikan berita gaming terbaru!</p>
            @if($currentUser)
                <a href="{{ route('news.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-3 rounded-lg font-medium transition shadow-lg shadow-primary/25"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Post Berita Pertama</a>
            @else
                <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">Login untuk memulai</a>
            @endif
        </div>
    @endforelse

    @if($newsList->hasPages())
        <div class="mt-8 flex justify-center">{{ $newsList->links() }}</div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .pagination span { color: #64748b !important; }
    .pagination a { color: #334155 !important; }
    .pagination .active span { background: #6366f1 !important; color: white !important; }
    .dark .pagination span { color: #94a3b8 !important; }
    .dark .pagination a { color: #e2e8f0 !important; }
</style>
@endpush

@push('scripts')
<script>
const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
async function toggleVote(newsId, type) { const upvoteBtn = document.getElementById(`upvote-btn-${newsId}`); const downvoteBtn = document.getElementById(`downvote-btn-${newsId}`); if (upvoteBtn) upvoteBtn.style.opacity = '0.6'; if (downvoteBtn) downvoteBtn.style.opacity = '0.6'; try { const response = await fetch(`/news/${newsId}/vote`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ type }) }); const data = await response.json(); if (data.success) { document.getElementById(`upvote-count-${newsId}`).textContent = data.upvotes; document.getElementById(`downvote-count-${newsId}`).textContent = data.downvotes; const scoreBadge = document.querySelector(`#news-${newsId} [class*="Score:"]`); if (scoreBadge) scoreBadge.textContent = `Score: ${data.score}`; if (type === 'upvote') { upvoteBtn?.classList.toggle('text-green-500', data.action !== 'removed'); downvoteBtn?.classList.remove('text-red-500'); } else { downvoteBtn?.classList.toggle('text-red-500', data.action !== 'removed'); upvoteBtn?.classList.remove('text-green-500'); } } else { if (data.error === 'Unauthorized') { window.location.href = '/login'; } else { alert(data.error || 'Gagal memberikan vote'); } } } catch (error) { console.error('Vote error:', error); alert('Terjadi kesalahan. Silakan coba lagi.'); } finally { if (upvoteBtn) upvoteBtn.style.opacity = '1'; if (downvoteBtn) downvoteBtn.style.opacity = '1'; } }

// GANTI SELURUH FUNGSI deleteNews lama dengan ini:
async function deleteNews(newsId) {
    if (!confirm('Hapus news ini? Tindakan ini tidak dapat dibatalkan.')) return;
    
    const newsEl = document.getElementById(`news-${newsId}`);
    if (!newsEl) return;

    // Loading state
    newsEl.style.opacity = '0.5';
    newsEl.style.pointerEvents = 'none';

    try {
        // ✅ FIX 405: Pakai POST + _method: DELETE
        const response = await fetch(`/news/${newsId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-HTTP-Method-Override': 'DELETE'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            newsEl.style.transition = 'all 0.3s ease';
            newsEl.style.opacity = '0';
            newsEl.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                newsEl.remove();
                if (document.querySelectorAll('article[id^="news-"]').length === 0) {
                    window.location.reload();
                }
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'News berhasil dihapus! 🗑️', type: 'success' }
                }));
            }, 300);
        } else {
            newsEl.style.opacity = '1';
            newsEl.style.pointerEvents = 'auto';
            alert(data.error || `Gagal menghapus (Error ${response.status})`);
        }
    } catch (error) {
        console.error('Delete error:', error);
        newsEl.style.opacity = '1';
        newsEl.style.pointerEvents = 'auto';
        alert('Terjadi kesalahan jaringan.');
    }
}

</script>
@endpush