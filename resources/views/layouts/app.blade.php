<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- ✅ ANTI-FLASH SCRIPT -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    
    <title>@yield('title', 'MyanGames')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>


    <script>window.csrfToken = '{{ csrf_token() }}';</script>
    
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">
    
    <!-- Navbar -->
    <nav class="bg-white dark:bg-card border-b border-slate-200 dark:border-gray-700 sticky top-0 z-50" 
         x-data="{ mobileMenuOpen: false }">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo -->
                <a href="{{ route('feed') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl">🎮</span>
                    <span class="text-xl font-bold bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent group-hover:opacity-90 transition">
                        MyanGames
                    </span>
                </a>

                <!-- Desktop Menu (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('feed') }}" class="hover:text-primary transition {{ request()->routeIs('feed') ? 'text-primary font-semibold' : 'text-slate-600 dark:text-gray-300' }}">
                        📰 Feed
                    </a>
                    <a href="{{ route('news') }}" class="hover:text-primary transition {{ request()->routeIs('news') ? 'text-primary font-semibold' : 'text-slate-600 dark:text-gray-300' }}">
                        🗞️ News
                    </a>
                </div>

                <!-- Right Side: Auth + Theme Toggle + Mobile Toggle -->
                <div class="flex items-center gap-2">
                    
                    <!-- Theme Toggle (Always Visible) -->
                    <x-theme-toggle />
                    
                    <!-- Desktop Auth (Hidden on Mobile) -->
                    <div class="hidden md:flex items-center gap-2">
                        @php $user = session('supabase_user'); @endphp
                        @if($user)
                            <a href="{{ route('profile', $user['id']) }}" 
                               class="flex items-center gap-2 bg-primary/20 hover:bg-primary/30 px-3 py-1.5 rounded-lg transition border border-primary/30">
                                <span class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-sm font-bold shadow">
                                    {{ strtoupper(substr($user['username'] ?? 'U', 0, 1)) }}
                                </span>
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-200">
                                    {{ $user['username'] ?? 'User' }}
                                </span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition p-1.5" title="Logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-600 dark:text-gray-300 hover:text-slate-900 dark:hover:text-white text-sm font-medium transition px-3 py-1.5">Login</a>
                            <a href="{{ route('register') }}" class="bg-primary hover:bg-primary-hover text-white px-4 py-1.5 rounded-lg text-sm font-medium transition shadow-lg shadow-primary/25">Register</a>
                        @endif
                    </div>

                    <!-- ✅ MOBILE MENU TOGGLE BUTTON (Hamburger) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="md:hidden p-2 rounded-lg text-slate-600 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-800 transition"
                            aria-label="Toggle menu"
                            :aria-expanded="mobileMenuOpen">
                        <!-- Hamburger Icon (show when menu closed) -->
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Close Icon (show when menu open) -->
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ✅ MOBILE MENU DROPDOWN (Alpine.js) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-slate-200 dark:border-gray-700 bg-white dark:bg-card">
            <div class="px-4 py-3 space-y-2">
                
                <!-- Navigation Links -->
                <a href="{{ route('feed') }}" 
                   @click="mobileMenuOpen = false"
                   class="block py-2 px-3 rounded-lg hover:bg-slate-100 dark:hover:bg-gray-800 transition {{ request()->routeIs('feed') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-700 dark:text-gray-300' }}">
                    📰 Feed
                </a>
                <a href="{{ route('news') }}" 
                   @click="mobileMenuOpen = false"
                   class="block py-2 px-3 rounded-lg hover:bg-slate-100 dark:hover:bg-gray-800 transition {{ request()->routeIs('news') ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-700 dark:text-gray-300' }}">
                    🗞️ News
                </a>
                
                <!-- Divider -->
                <div class="border-t border-slate-200 dark:border-gray-700 my-2"></div>
                
                <!-- Mobile Auth Buttons -->
                @php $user = session('supabase_user'); @endphp
                @if($user)
                    <!-- User Info -->
                    <div class="flex items-center gap-3 py-2 px-3">
                        <span class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold shadow">
                            {{ strtoupper(substr($user['username'] ?? 'U', 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $user['username'] ?? 'User' }}</p>
                            <a href="{{ route('profile', $user['id']) }}" @click="mobileMenuOpen = false" class="text-xs text-primary hover:underline">Lihat Profil</a>
                        </div>
                    </div>
                    
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" 
                                @click="mobileMenuOpen = false"
                                class="w-full text-left py-2 px-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                @else
                    <!-- Guest Auth Buttons -->
                    <div class="grid grid-cols-2 gap-2 py-2">
                        <a href="{{ route('login') }}" 
                           @click="mobileMenuOpen = false"
                           class="text-center py-2 px-3 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-800 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           @click="mobileMenuOpen = false"
                           class="text-center py-2 px-3 bg-primary hover:bg-primary-hover text-white rounded-lg transition">
                            Register
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-6xl mx-auto px-4 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-card border-t border-slate-200 dark:border-gray-700 py-6 mt-auto">
        <div class="max-w-6xl mx-auto px-4 text-center text-slate-500 dark:text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} MyanGames. Made with ❤️ for gamers.</p>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div x-data="toast()" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         class="fixed bottom-4 right-4 z-50 max-w-sm"
         @toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 4000)">
        
        <div class="p-4 rounded-xl shadow-glow-lg border"
             :class="{
                'bg-success/90 border-success/30 text-white': type === 'success',
                'bg-danger/90 border-danger/30 text-white': type === 'error',
                'bg-primary/90 border-primary/30 text-white': type === 'info'
             }">
            <div class="flex items-start gap-3">
                <span x-text="message"></span>
                <button @click="show = false" class="ml-auto text-white/70 hover:text-white">&times;</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function toast() {
        return { show: false, message: '', type: 'info' }
    }
    </script>
    @endpush

    @stack('scripts')



</body>
</html>