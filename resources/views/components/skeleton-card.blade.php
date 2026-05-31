{{-- resources/views/components/skeleton-card.blade.php --}}
{{-- Reusable skeleton loader for Feed/News cards --}}

<div class="bg-card rounded-xl border border-gray-700 overflow-hidden animate-pulse mb-6">
    <!-- Header Skeleton -->
    <div class="p-4 flex items-center gap-3 border-b border-gray-700/50">
        <!-- Avatar Placeholder -->
        <div class="w-10 h-10 rounded-full bg-gray-700 animate-shimmer"></div>
        
        <!-- User Info Placeholder -->
        <div class="flex-1 space-y-2">
            <div class="h-4 w-24 bg-gray-700 rounded animate-shimmer"></div>
            <div class="h-3 w-16 bg-gray-700 rounded animate-shimmer"></div>
        </div>
        
        <!-- Game Badge Placeholder -->
        <div class="h-6 w-20 bg-gray-700 rounded-full animate-shimmer"></div>
    </div>

    <!-- Image Placeholder (Optional - shown for posts with images) -->
    <div class="h-48 md:h-64 w-full bg-gray-800 animate-shimmer"></div>

    <!-- Content Placeholder -->
    <div class="p-4 space-y-3">
        <div class="h-4 w-full bg-gray-700 rounded animate-shimmer"></div>
        <div class="h-4 w-5/6 bg-gray-700 rounded animate-shimmer"></div>
        <div class="h-4 w-4/6 bg-gray-700 rounded animate-shimmer"></div>
    </div>

    <!-- Actions Placeholder -->
    <div class="px-4 py-3 border-t border-gray-700/50 flex items-center gap-6">
        <div class="h-5 w-12 bg-gray-700 rounded animate-shimmer"></div>
        <div class="h-5 w-12 bg-gray-700 rounded animate-shimmer"></div>
        <div class="h-5 w-16 bg-gray-700 rounded animate-shimmer"></div>
    </div>
</div>