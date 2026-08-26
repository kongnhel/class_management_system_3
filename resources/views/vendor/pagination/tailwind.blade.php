@if ($paginator->hasPages())
    <div class="flex flex-col items-center gap-3">
        @if ($paginator->firstItem())
            <p class="text-sm font-medium text-slate-500">
                {{ __('បង្ហាញ :min ដល់ :max ក្នុងចំណោម :total លទ្ធផល', [
                    'min'   => $paginator->firstItem(),
                    'max'   => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ]) }}
            </p>
        @endif

        <nav role="navigation" aria-label="Pagination Navigation" class="inline-flex items-center gap-1.5">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-300 cursor-not-allowed select-none" aria-disabled="true">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-colors duration-150">&lsaquo;</a>
            @endif

            {{-- Numbered Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-8 h-10 text-slate-400 select-none">&hellip;</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center min-w-[2.5rem] h-10 px-3 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-md shadow-emerald-200 select-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center min-w-[2.5rem] h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-colors duration-150">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-colors duration-150">&rsaquo;</a>
            @else
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-300 cursor-not-allowed select-none" aria-disabled="true">&rsaquo;</span>
            @endif

        </nav>
    </div>
@endif
