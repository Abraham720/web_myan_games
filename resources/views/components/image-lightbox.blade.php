{{-- resources/views/components/image-lightbox.blade.php --}}
{{-- Reusable Image Lightbox Component with Alpine.js --}}

<div 
    x-data="{ 
        isOpen: false, 
        imageUrl: '', 
        imageAlt: '',
        open(src, alt) {
            this.imageUrl = src;
            this.imageAlt = alt;
            this.isOpen = true;
            document.body.style.overflow = 'hidden'; // Prevent scroll
        },
        close() {
            this.isOpen = false;
            document.body.style.overflow = ''; // Restore scroll
        },
        handleKeydown(e) {
            if (e.key === 'Escape') this.close();
        }
    }"
    @keydown.window="handleKeydown"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm"
    @click.self="close()"
    style="display: none;"
>
    <!-- Close Button -->
    <button 
        @click="close()"
        class="absolute top-4 right-4 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition z-10"
        title="Tutup (ESC)"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Image Container -->
    <div class="relative max-w-5xl max-h-[90vh] mx-4">
        <img 
            :src="imageUrl" 
            :alt="imageAlt"
            class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
            @load="setTimeout(() => $el.style.opacity = 1, 50)"
            style="transition: opacity 0.2s ease; opacity: 0;"
        >
        
        <!-- Image Caption (Optional) -->
        <p x-show="imageAlt" class="text-center text-white/80 text-sm mt-3" x-text="imageAlt"></p>
    </div>

    <!-- Hint: Click outside or press ESC to close -->
    <p class="absolute bottom-4 text-white/50 text-xs">Klik luar atau tekan ESC untuk tutup</p>
</div>