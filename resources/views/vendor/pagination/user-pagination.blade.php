@if ($paginator->hasPages())
    <div class="pager">
        <p><span>{{ $paginator->total() }}</span>件（{{ ($paginator->currentPage()-1) * $paginator->perPage() + 1 }}件〜 @if ( $paginator->currentPage() * $paginator->perPage() > $paginator->total()) {{ $paginator->total() }} @else {{ $paginator->currentPage() * $paginator->perPage() }} @endif件）</p>
        <ul>
            <li class="return_to_begin"><a href="{{$paginator->url(1)}}"></a></li>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="back_one">
                    <a href="#" onclick="event.preventDefault();"></a>
                </li>
            @else
                <li class="back_one">
                    <a href="{{ $paginator->previousPageUrl() }}"></a>
                </li>
            @endif

            <div class="page_num">
                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="checked"><a href="#" onclick="event.preventDefault();">{{ $element }}</a></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="checked"><a href="#" onclick="event.preventDefault();">{{ $page }}</a></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="advance_one">
                    <a href="{{ $paginator->nextPageUrl() }}"></a>
                </li>
            @else
                <li class="advance_one disabled" >
                    <a href="#" onclick="event.preventDefault();"></a>
                </li>
            @endif
            <li class="go_end"><a href="{{$paginator->url($paginator->lastPage())}}"></a></li>
        </ul>
    </div>
@endif
