@props([
    'action' => '',
    'placeholder' => 'Cari game, caption...',
    'initialQuery' => ''
])

<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <form action="{{ route($action) }}" method="GET" class="relative">
        <!-- Input Container dengan flex alignment -->
        <div class="relative flex items-center">
            
            <!-- Search Icon -->
            <span class="absolute left-4 z-10 text-slate-400 dark:text-gray-400 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            
            <!-- Input Field - Light/Dark Mode Support -->
            <input 
                type="search" 
                name="q" 
                value="{{ $initialQuery }}"
                placeholder="{{ $placeholder }}"
                class="w-full pl-12 pr-10 py-3 
                       bg-slate-100 dark:bg-dark/80 
                       border border-slate-300 dark:border-gray-600 
                       rounded-xl 
                       text-slate-900 dark:text-white 
                       placeholder-slate-400 dark:placeholder-gray-400
                       focus:ring-2 focus:ring-primary/50 focus:border-primary focus:outline-none
                       hover:border-primary/50 dark:hover:border-gray-500 
                       transition-all duration-200
                       leading-normal"
                @focus="open = true"
                x-ref="searchInput"
            >
            
            <!-- Clear Button -->
            @if($initialQuery)
            <a href="{{ route(str_replace('search.', '', $action)) }}" 
               class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-gray-400 hover:text-slate-600 dark:hover:text-white transition p-1"
               title="Reset pencarian">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
            @endif
        </div>
    </form>
    
    <!-- Search Tips Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 -translate-y-2" 
         x-transition:enter-end="opacity-100 translate-y-0" 
         class="absolute z-20 mt-2 w-full 
                bg-white dark:bg-card 
                border border-slate-200 dark:border-gray-700 
                rounded-xl shadow-xl p-3 text-sm text-slate-600 dark:text-gray-300">
        <p class="font-medium text-slate-900 dark:text-white mb-1">💡 Tips Pencarian:</p>
        <ul class="space-y-1 text-xs">
            <li>• Gunakan kata kunci game: <code class="bg-slate-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-slate-700 dark:text-gray-300">Valorant</code></li>
            <li>• Cari berdasarkan caption: <code class="bg-slate-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-slate-700 dark:text-gray-300">"tips build"</code></li>
            <li>• Tekan <kbd class="bg-slate-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-slate-700 dark:text-gray-300">Enter</kbd> untuk mencari</li>
        </ul>
    </div>
</div>