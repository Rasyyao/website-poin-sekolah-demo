@if ($paginator->firstItem())
<div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 px-1">

    {{-- "Showing X to Y of Z" --}}
    <p class="text-sm text-gray-400">
        Menampilkan
        <span class="font-semibold text-gray-200">{{ $paginator->firstItem() }}</span>
        –
        <span class="font-semibold text-gray-200">{{ $paginator->lastItem() }}</span>
        dari
        <span class="font-semibold text-gray-200">{{ $paginator->total() }}</span>
        data
    </p>

    {{-- Page buttons (only when multiple pages exist) --}}
    @if ($paginator->hasPages())
    <div class="flex items-center gap-1">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-700/50 bg-surface-light text-gray-600 cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-700/50 bg-surface-light text-gray-400 hover:bg-surface-lighter hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-700/50 bg-surface-light text-gray-500 text-sm select-none">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-primary-500 bg-primary-600 text-white text-sm font-semibold shadow-lg shadow-primary-500/20 cursor-default select-none">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-700/50 bg-surface-light text-gray-400 hover:bg-surface-lighter hover:text-gray-200 text-sm transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-700/50 bg-surface-light text-gray-400 hover:bg-surface-lighter hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-700/50 bg-surface-light text-gray-600 cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>
    @endif
</div>
@endif

