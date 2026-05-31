{{-- resources/views/components/theme-toggle.blade.php --}}
{{-- Dark/Light mode toggle button with Alpine.js + localStorage persistence --}}

<button 
    type="button"
    @click="toggleTheme()"
    x-data="{
        theme: localStorage.getItem('theme') || 'dark',
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            this.applyTheme();
        },
        applyTheme() {
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        init() {
            this.applyTheme();
        }
    }"
    class="p-2 rounded-lg bg-card border border-gray-700 hover:border-primary 
           text-gray-400 hover:text-white transition-all duration-200 
           hover:shadow-glow hover:scale-105 active:scale-95"
    title="Ganti Tema: Dark/Light"
    aria-label="Toggle dark/light mode"
>
    <!-- ☀️ Sun Icon (Shown in Dark Mode - click to switch to Light) -->
    <svg x-show="theme === 'dark'" 
         class="w-5 h-5 transition-transform duration-200" 
         fill="none" 
         viewBox="0 0 24 24" 
         stroke="currentColor"
         @click="$el.style.transform = 'rotate(180deg)'; setTimeout(() => $el.style.transform = '', 200)">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    
    <!-- 🌙 Moon Icon (Shown in Light Mode - click to switch to Dark) -->
    <svg x-show="theme === 'light'" 
         class="w-5 h-5 transition-transform duration-200" 
         fill="none" 
         viewBox="0 0 24 24" 
         stroke="currentColor"
         @click="$el.style.transform = 'rotate(-180deg)'; setTimeout(() => $el.style.transform = '', 200)">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>