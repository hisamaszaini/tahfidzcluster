@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Pagination Info Text (Indonesian) -->
        <div class="text-xs text-slate-400 font-medium leading-relaxed order-2 sm:order-1 text-center sm:text-left">
            Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} hasil
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center space-x-1 order-1 sm:order-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-300 rounded-xl text-xs font-semibold cursor-not-allowed border border-slate-100">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition duration-150 shadow-xs active:scale-95"
                   title="Halaman Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl text-xs font-semibold select-none cursor-default border border-slate-100">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-xs select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" 
                               class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition duration-150 shadow-xs active:scale-95">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition duration-150 shadow-xs active:scale-95"
                   title="Halaman Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-300 rounded-xl text-xs font-semibold cursor-not-allowed border border-slate-100">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
@endif
