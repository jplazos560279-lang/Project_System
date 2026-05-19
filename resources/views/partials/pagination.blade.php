@if($paginator->hasPages())
<nav class="pagination">
    {{-- Previous Page Link --}}
    @if($paginator->onFirstPage())
        <span class="page-item disabled">&laquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-item">&laquo;</a>
    @endif

    {{-- Simple Page Numbers --}}
    @for($i = 1; $i <= $paginator->lastPage(); $i++)
        @if($i == $paginator->currentPage())
            <span class="page-item active">{{ $i }}</span>
        @else
            <a href="{{ $paginator->url($i) }}" class="page-item">{{ $i }}</a>
        @endif
    @endfor

    {{-- Next Page Link --}}
@if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-item" style="font-size: 10px; padding: 0 8px;">&raquo;</a>
    @else
        <span class="page-item disabled" style="font-size: 10px; padding: 0 8px;">&raquo;</span>
    @endif
</nav>
@endif
