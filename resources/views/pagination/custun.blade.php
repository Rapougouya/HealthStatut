@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Affichage de 
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    à
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    sur
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    résultats
                </p>
            </div>
            <div>
                <ul class="flex space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                            <span>&laquo;</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-md">
                                &laquo;
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md">
                                <span>{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="px-3 py-1 bg-medical-blue text-white rounded-md">
                                        <span>{{ $page }}</span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-md">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-md">
                                &raquo;
                            </a>
                        </li>
                    @else
                        <li class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                            <span>&raquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif