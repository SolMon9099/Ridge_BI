@if ($paginator->total())
    <div class="tour-content">
        <p class="float-l pagecount">全{{ $paginator->total() }}件中 {{ ($paginator->currentPage()-1) * $paginator->perPage() + 1 }}件〜 @if ( $paginator->currentPage() * $paginator->perPage() > $paginator->total()) {{ $paginator->total() }} @else {{ $paginator->currentPage() * $paginator->perPage() }} @endif 件を表示</p>
        <div class="float-l pager">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <a href="#" onclick="event.preventDefault();">＜</a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}">＜</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    {{ $element }}
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}">＞</a>
            @else
                <a href="#" onclick="event.preventDefault();">＞</a>
            @endif
        </div>
    </div>
@endif
