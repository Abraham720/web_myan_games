@extends('layouts.app')

@section('title', $profileModel->username . ' • MyanGames')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Profile Header Card -->
    <div class="bg-white dark:bg-card rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-primary via-secondary to-accent"></div>
        
        <div class="px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-12 mb-4">
                <div class="flex items-end gap-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-primary to-secondary rounded-2xl border-4 border-white dark:border-card flex items-center justify-center text-3xl font-bold text-white shadow-xl">
                        {{ strtoupper(substr($profileModel->username ?? 'U', 0, 1)) }}
                    </div>
                    <div class="mb-2">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $profileModel->username ?? 'Anonymous' }}</h1>
                        <p class="text-slate-500 dark:text-gray-400 text-sm">Bergabung sejak {{ $profileModel->created_at?->format('F Y') ?? 'Unknown' }}</p>
                    </div>
                </div>
                
                @php $currentUser = session('supabase_user'); @endphp
                @if($currentUser && $currentUser['id'] === $profileModel->id)
                <a href="{{ route('profile.edit', $profileModel->id) }}" 
                   class="mb-2 inline-flex items-center gap-2 px-4 py-2 bg-primary/20 hover:bg-primary/30 text-primary border border-primary/30 rounded-lg text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Profil
                </a>
                @endif
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-200 dark:border-gray-700">
                <div class="text-center p-3 bg-slate-100 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $profileModel->posts_count ?? $profileModel->posts()->count() }}</p>
                    <p class="text-xs text-slate-500 dark:text-gray-400">Posts</p>
                </div>
                <div class="text-center p-3 bg-slate-100 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $profileModel->news_count ?? $profileModel->news()->count() }}</p>
                    <p class="text-xs text-slate-500 dark:text-gray-400">News</p>
                </div>
                <div class="text-center p-3 bg-slate-100 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-2xl font-bold text-green-500 dark:text-green-400">{{ $profileModel->total_likes ?? 0 }}</p>
                    <p class="text-xs text-slate-500 dark:text-gray-400">Likes Diterima</p>
                </div>
                <div class="text-center p-3 bg-slate-100 dark:bg-gray-800/50 rounded-lg">
                    <p class="text-2xl font-bold text-accent">{{ $profileModel->total_upvotes ?? 0 }}</p>
                    <p class="text-xs text-slate-500 dark:text-gray-400">Upvotes Diterima</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Tabs -->
    <div class="bg-white dark:bg-card rounded-2xl border border-slate-200 dark:border-gray-700 overflow-hidden">
        <div class="flex border-b border-slate-200 dark:border-gray-700">
            <button onclick="switchTab('posts')" id="tab-posts" class="flex-1 py-4 text-center font-medium text-primary border-b-2 border-primary transition">📰 Posts</button>
            <button onclick="switchTab('news')" id="tab-news" class="flex-1 py-4 text-center font-medium text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white transition">🗞️ News</button>
        </div>

        <!-- Tab: Posts -->
        <div id="content-posts" class="p-4 space-y-4">
            @forelse($posts as $post)
                <article class="bg-slate-100 dark:bg-gray-800/50 rounded-xl p-4 hover:bg-slate-200 dark:hover:bg-gray-800 transition">
                    <div class="flex items-start gap-3">
                        @if($post->image_url)
                            <img src="{{ $post->image_url }}" alt="" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-slate-200 dark:bg-gray-700 rounded text-xs text-slate-700 dark:text-gray-300 font-mono">{{ $post->game_title }}</span>
                                <span class="text-xs text-slate-500 dark:text-gray-500">{{ $post->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 dark:text-gray-300 text-sm line-clamp-2">{{ $post->caption }}</p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-slate-500 dark:text-gray-500">
                                <span>👍 {{ $post->reactions->where('type', 'like')->count() }}</span>
                                <span>💬 {{ $post->comments->count() }}</span>
                                <a href="{{ route('feed') }}#post-{{ $post->id }}" class="text-primary hover:underline ml-auto">Lihat →</a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-center text-slate-500 dark:text-gray-400 py-8">Belum ada posts.</p>
            @endforelse
            @if($posts->hasPages())<div class="pt-4">{{ $posts->links() }}</div>@endif
        </div>

        <!-- Tab: News -->
        <div id="content-news" class="p-4 space-y-4 hidden">
            @forelse($news as $item)
                <article class="bg-slate-100 dark:bg-gray-800/50 rounded-xl p-4 hover:bg-slate-200 dark:hover:bg-gray-800 transition">
                    <div class="flex items-start gap-3">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-accent/20 text-accent rounded text-xs font-mono">{{ $item->game_title }}</span>
                                <span class="text-xs text-slate-500 dark:text-gray-500">{{ $item->created_at?->diffForHumans() }}</span>
                                <span class="ml-auto text-xs font-bold text-accent">Score: {{ $item->score }}</span>
                            </div>
                            <p class="text-slate-700 dark:text-gray-300 text-sm line-clamp-2">{{ $item->caption }}</p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-slate-500 dark:text-gray-500">
                                <span>⬆️ {{ $item->upvote_count }}</span>
                                <span>⬇️ {{ $item->downvote_count }}</span>
                                <a href="{{ route('news') }}#news-{{ $item->id }}" class="text-primary hover:underline ml-auto">Baca →</a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <p class="text-center text-slate-500 dark:text-gray-400 py-8">Belum ada news.</p>
            @endforelse
            @if($news->hasPages())<div class="pt-4">{{ $news->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pagination span { color: #64748b !important; }
    .pagination a { color: #334155 !important; }
    .pagination .active span { background: #6366f1 !important; color: white !important; }
    .dark .pagination span { color: #94a3b8 !important; }
    .dark .pagination a { color: #e2e8f0 !important; }
</style>
@endpush

@push('scripts')
<script>
function switchTab(tab) {
    document.getElementById('content-posts').classList.add('hidden');
    document.getElementById('content-news').classList.add('hidden');
    document.getElementById('tab-posts').classList.remove('text-primary', 'border-primary', 'border-b-2');
    document.getElementById('tab-posts').classList.add('text-slate-500', 'dark:text-gray-400');
    document.getElementById('tab-news').classList.remove('text-primary', 'border-primary', 'border-b-2');
    document.getElementById('tab-news').classList.add('text-slate-500', 'dark:text-gray-400');
    if (tab === 'posts') {
        document.getElementById('content-posts').classList.remove('hidden');
        document.getElementById('tab-posts').classList.add('text-primary', 'border-primary', 'border-b-2');
        document.getElementById('tab-posts').classList.remove('text-slate-500', 'dark:text-gray-400');
    } else {
        document.getElementById('content-news').classList.remove('hidden');
        document.getElementById('tab-news').classList.add('text-primary', 'border-primary', 'border-b-2');
        document.getElementById('tab-news').classList.remove('text-slate-500', 'dark:text-gray-400');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('[class*="bg-green-500"], [class*="bg-red-500"]');
    flashMessages.forEach(msg => { setTimeout(() => { msg.style.transition = 'opacity 0.5s ease'; msg.style.opacity = '0'; setTimeout(() => msg.remove(), 500); }, 5000); });
});
</script>
@endpush