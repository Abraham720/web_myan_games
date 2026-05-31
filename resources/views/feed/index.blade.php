@extends('layouts.app')

@section('title', 'Feed • MyanGames')

@section('content')
<div class="space-y-6">
    
    <!-- Header Section dengan Search + Create Post Button -->
    <div class="space-y-4 mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">🎮 Game Feed</h1>
            <div class="flex items-center gap-2">
                <button class="px-4 py-2 bg-primary/20 text-primary rounded-lg text-sm font-medium hover:bg-primary/30 transition">
                    Latest
                </button>
                <button class="px-4 py-2 text-slate-500 dark:text-gray-400 hover:text-slate-900 dark:hover:text-white text-sm font-medium transition">
                    Popular
                </button>
                
                <!-- ✨ CREATE POST BUTTON (NEW) -->
                @php $currentUser = session('supabase_user'); @endphp
                @if($currentUser)
                <a href="{{ route('post.create') }}" 
                   class="ml-2 inline-flex items-center gap-1.5 bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-lg shadow-primary/25 hover:scale-105 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Post
                </a>
                @endif
            </div>
        </div>
        
        <!-- Search Bar -->
        <x-search-bar 
            action="search.feed" 
            placeholder="Cari game, caption..." 
            :initialQuery="$query ?? ''" 
        />
    </div>

    <!-- ✨ SKELETON LOADING -->
    <div x-data="{ initialLoad: true }" 
         x-show="initialLoad" 
         x-init="setTimeout(() => initialLoad = false, 800)"
         class="space-y-6">
        @for($i = 0; $i < 3; $i++)
            <x-skeleton-card />
        @endfor
    </div>

    <!-- Posts List -->
    @forelse ($posts as $post)
        <article class="bg-white dark:bg-card rounded-xl border border-slate-200 dark:border-gray-700 
                        overflow-hidden hover:border-primary/50 hover:shadow-glow-lg hover:-translate-y-1
                        transition-all duration-300 group animate-fade-in" 
                 id="post-{{ $post->id }}">
            
            <!-- Post Header -->
            <div class="p-4 flex items-center gap-3 border-b border-slate-200 dark:border-gray-700/50">
                <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary 
                            rounded-full flex items-center justify-center text-white 
                            font-bold text-sm shadow-lg animate-float">
                    {{ strtoupper(substr($post->user->username ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 dark:text-white truncate">
                        {{ $post->user->username ?? 'Anonymous' }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-gray-400">
                        {{ $post->created_at?->diffForHumans() }}
                    </p>
                </div>
                <span class="px-3 py-1 bg-slate-200 dark:bg-gray-700/50 
                             rounded-full text-xs text-slate-700 dark:text-gray-300 font-mono">
                    {{ $post->game_title }}
                </span>
            </div>

            <!-- Post Content -->
            <div class="p-4">
                <p class="text-slate-700 dark:text-gray-200 leading-relaxed mb-3">
                    {{ $post->caption }}
                </p>
                
                @if($post->image_url)
                    <div class="rounded-lg overflow-hidden bg-slate-100 dark:bg-gray-800 mb-3">
                        <img src="{{ $post->image_url }}" alt="Post Image" 
                             class="w-full h-auto max-h-96 object-cover hover:scale-105 transition duration-300"
                             onerror="this.parentElement.style.display='none'"
                             onload="this.classList.add('loaded')">
                    </div>
                @endif
            </div>

            <!-- Post Actions -->
            <div class="px-4 py-3 border-t border-slate-200 dark:border-gray-700/50 flex items-center gap-6 text-slate-500 dark:text-gray-400">
                
                <!-- LIKE Button -->
                <button 
                    onclick="toggleReaction('{{ $post->id }}', 'like')"
                    class="flex items-center gap-2 hover:text-green-500 dark:hover:text-green-400 transition group/btn 
                           {{ $post->reactions->contains('type', 'like') ? 'text-green-500 dark:text-green-400' : '' }}"
                    id="like-btn-{{ $post->id }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover/btn:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    <span class="text-sm font-medium" id="like-count-{{ $post->id }}">
                        {{ $post->reactions->where('type', 'like')->count() }}
                    </span>
                </button>
                
                <!-- DISLIKE Button -->
                <button 
                    onclick="toggleReaction('{{ $post->id }}', 'dislike')"
                    class="flex items-center gap-2 hover:text-red-500 dark:hover:text-red-400 transition group/btn 
                           {{ $post->reactions->contains('type', 'dislike') ? 'text-red-500 dark:text-red-400' : '' }}"
                    id="dislike-btn-{{ $post->id }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover/btn:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                    </svg>
                    <span class="text-sm font-medium" id="dislike-count-{{ $post->id }}">
                        {{ $post->reactions->where('type', 'dislike')->count() }}
                    </span>
                </button>
                
                <!-- COMMENT Button -->
                <button 
                    onclick="toggleCommentForm('{{ $post->id }}')"
                    class="flex items-center gap-2 hover:text-primary transition group/btn"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover/btn:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm font-medium" id="comment-count-{{ $post->id }}">{{ $post->comments->count() }}</span>
                </button>

                <!-- Edit & Delete Buttons -->
                @if($currentUser && $currentUser['id'] === $post->user_id)
                <div class="ml-auto flex items-center gap-1 border-l border-slate-200 dark:border-gray-700 pl-4">
                    <a href="{{ route('post.edit', $post->id) }}" 
                       class="text-slate-400 dark:text-gray-500 hover:text-primary transition p-1.5 rounded hover:bg-slate-100 dark:hover:bg-gray-800"
                       title="Edit post">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <button onclick="confirmDelete('{{ $post->id }}')" 
                            class="text-slate-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition p-1.5 rounded hover:bg-slate-100 dark:hover:bg-gray-800"
                            title="Hapus post">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                @endif
            </div>

            <!-- Comment Section -->
            <div id="comment-section-{{ $post->id }}" class="hidden px-4 pb-4 pt-2 border-t border-slate-200 dark:border-gray-700/30">
                @if($currentUser)
                <form onsubmit="submitComment(event, '{{ $post->id }}')" class="flex gap-2 mb-4">
                    <input type="text" name="content" placeholder="Tulis komentar..." 
                           class="flex-1 px-4 py-2 bg-slate-50 dark:bg-dark border border-slate-300 dark:border-gray-600 rounded-lg text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition"
                           required maxlength="500">
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg text-sm font-medium transition whitespace-nowrap">Kirim</button>
                </form>
                @else
                <p class="text-sm text-slate-500 dark:text-gray-500 mb-4">
                    <a href="{{ route('login') }}" class="text-primary hover:underline">Login</a> untuk berkomentar.
                </p>
                @endif

                <div id="comments-list-{{ $post->id }}" class="space-y-3">
                    @foreach($post->comments->sortByDesc('created_at') as $comment)
                    <div class="flex gap-3 text-sm" id="comment-{{ $comment->id }}">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($comment->user->username ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 bg-slate-100 dark:bg-gray-800/50 rounded-lg px-3 py-2">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-primary">{{ $comment->user->username ?? 'User' }}</span>
                                <span class="text-xs text-slate-500 dark:text-gray-500">{{ $comment->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 dark:text-gray-300 break-words">{{ $comment->content }}</p>
                        </div>
                        @if($currentUser && $currentUser['id'] === $comment->user_id)
                        <button onclick="deleteComment('{{ $comment->id }}')" class="text-slate-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition self-start p-1" title="Hapus komentar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </article>
    @empty
        <div class="text-center py-16 bg-white dark:bg-card rounded-xl border border-slate-200 dark:border-gray-700 border-dashed">
            <div class="text-6xl mb-4">🎮</div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">Belum ada postingan</h3>
            <p class="text-slate-500 dark:text-gray-400 mb-6">Jadilah yang pertama membagikan pengalaman gamingmu!</p>
            @if($currentUser)
                <a href="{{ route('post.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-3 rounded-lg font-medium transition shadow-lg shadow-primary/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Post Pertama
                </a>
            @else
                <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">Login untuk memulai</a>
            @endif
        </div>
    @endforelse

    @if($posts->hasPages())
        <div class="mt-8 flex justify-center">{{ $posts->links() }}</div>
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
    @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .comment-new { animation: slideIn 0.3s ease-out; }
    @keyframes fadeOut { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(0.95); } }
    .post-deleting { animation: fadeOut 0.3s ease forwards; }
    img { transition: opacity 0.3s ease; opacity: 0; }
    img.loaded { opacity: 1; }
</style>
@endpush

@push('scripts')
<script>
const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
function toggleCommentForm(postId) { const section = document.getElementById(`comment-section-${postId}`); section.classList.toggle('hidden'); if (!section.classList.contains('hidden')) { const input = section.querySelector('input[name="content"]'); if (input) input.focus(); } }
async function submitComment(event, postId) { event.preventDefault(); const form = event.target; const input = form.querySelector('input[name="content"]'); const content = input.value.trim(); const submitBtn = form.querySelector('button[type="submit"]'); if (!content) return; submitBtn.disabled = true; submitBtn.innerHTML = '<span class="animate-pulse">Mengirim...</span>'; try { const response = await fetch(`/post/${postId}/comment`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ content }) }); const data = await response.json(); if (data.success) { const commentsList = document.getElementById(`comments-list-${postId}`); const newComment = data.comment; const commentHtml = `<div class="flex gap-3 text-sm comment-new" id="comment-${newComment.id}"><div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">${newComment.user.username?.charAt(0).toUpperCase()}</div><div class="flex-1 bg-slate-100 dark:bg-gray-800/50 rounded-lg px-3 py-2"><div class="flex items-center gap-2 mb-1"><span class="font-semibold text-primary">${newComment.user.username}</span><span class="text-xs text-slate-500 dark:text-gray-500">${newComment.created_at}</span></div><p class="text-slate-700 dark:text-gray-300 break-words">${newComment.content}</p></div></div>`; commentsList.insertAdjacentHTML('afterbegin', commentHtml); updateCommentCount(postId, 1); input.value = ''; } else { alert(data.error || 'Gagal mengirim komentar'); } } catch (error) { console.error('Comment error:', error); alert('Terjadi kesalahan. Silakan coba lagi.'); } finally { submitBtn.disabled = false; submitBtn.textContent = 'Kirim'; } }
async function deleteComment(commentId) { if (!confirm('Hapus komentar ini?')) return; try { const response = await fetch(`/comment/${commentId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } }); const data = await response.json(); if (data.success) { const commentEl = document.getElementById(`comment-${commentId}`); if (commentEl) { commentEl.style.opacity = '0'; commentEl.style.transform = 'translateX(-20px)'; commentEl.style.transition = 'all 0.3s ease'; setTimeout(() => commentEl.remove(), 300); const postCard = commentEl.closest('article'); if (postCard) { const postId = postCard.id.replace('post-', ''); updateCommentCount(postId, -1); } } } else { alert(data.error || 'Gagal menghapus komentar'); } } catch (error) { console.error('Delete error:', error); alert('Terjadi kesalahan. Silakan coba lagi.'); } }
async function toggleReaction(postId, type) { const likeBtn = document.getElementById(`like-btn-${postId}`); const dislikeBtn = document.getElementById(`dislike-btn-${postId}`); if (likeBtn) likeBtn.style.opacity = '0.6'; if (dislikeBtn) dislikeBtn.style.opacity = '0.6'; try { const response = await fetch(`/post/${postId}/reaction`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ type }) }); const data = await response.json(); if (data.success) { document.getElementById(`like-count-${postId}`).textContent = data.counts.likes; document.getElementById(`dislike-count-${postId}`).textContent = data.counts.dislikes; if (type === 'like') { likeBtn?.classList.toggle('text-green-500', data.action !== 'removed'); dislikeBtn?.classList.remove('text-red-500'); } else { dislikeBtn?.classList.toggle('text-red-500', data.action !== 'removed'); likeBtn?.classList.remove('text-green-500'); } } else { if (data.error === 'Unauthorized') { window.location.href = '/login'; } else { alert(data.error || 'Gagal memberikan reaksi'); } } } catch (error) { console.error('Reaction error:', error); alert('Terjadi kesalahan. Silakan coba lagi.'); } finally { if (likeBtn) likeBtn.style.opacity = '1'; if (dislikeBtn) dislikeBtn.style.opacity = '1'; } }
async function confirmDelete(postId) { if (!confirm('Yakin ingin menghapus post ini? Tindakan ini tidak dapat dibatalkan.')) return; try { const response = await fetch(`/post/${postId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } }); if (response.ok) { const postEl = document.getElementById(`post-${postId}`); if (postEl) { postEl.classList.add('post-deleting'); setTimeout(() => { postEl.remove(); if (document.querySelectorAll('article[id^="post-"]').length === 0) { window.location.reload(); } }, 300); } } else { const data = await response.json(); alert(data.message || 'Gagal menghapus post. Silakan coba lagi.'); } } catch (error) { console.error('Delete post error:', error); alert('Terjadi kesalahan jaringan. Silakan coba lagi.'); } }
function updateCommentCount(postId, delta) { const counter = document.getElementById(`comment-count-${postId}`); if (counter) { const current = parseInt(counter.textContent) || 0; counter.textContent = Math.max(0, current + delta); } }
document.addEventListener('DOMContentLoaded', function() { const flashMessages = document.querySelectorAll('[class*="bg-green-500"], [class*="bg-red-500"]'); flashMessages.forEach(msg => { setTimeout(() => { msg.style.transition = 'opacity 0.5s ease'; msg.style.opacity = '0'; setTimeout(() => msg.remove(), 500); }, 5000); }); document.querySelectorAll('img').forEach(img => { if (img.complete) { img.classList.add('loaded'); } else { img.addEventListener('load', () => img.classList.add('loaded')); } }); });
</script>
@endpush