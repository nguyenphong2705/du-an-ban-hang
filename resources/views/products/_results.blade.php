{{-- Khối kết quả sản phẩm: dùng chung cho render lần đầu và cập nhật AJAX (X-Partial: products) --}}
<div class="bg-white px-6 py-4 rounded-2xl border border-sage-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
    <p class="text-sm text-stone-600">
        Hiển thị <span class="font-bold text-sage-600">{{ $products->total() }}</span> sản phẩm phù hợp
    </p>
    <div class="flex flex-wrap gap-2">
        @if($activeCategory)
            <span class="text-xs bg-sage-50 text-sage-700 px-3 py-1 rounded-full font-medium">{{ $activeCategory->name }}</span>
        @endif
        @if($activeBrand)
            <span class="text-xs bg-sage-50 text-sage-700 px-3 py-1 rounded-full font-medium">{{ $activeBrand->name }}</span>
        @endif
        @if($filters['skin_type'])
            <span class="text-xs bg-sage-50 text-sage-700 px-3 py-1 rounded-full font-medium">Loại da: {{ $filters['skin_type'] }}</span>
        @endif
    </div>
</div>

@if($categoryNotFound)
    <div class="bg-white rounded-3xl border border-amber-200 p-16 text-center space-y-4">
        <p class="font-serif text-xl font-bold text-stone-700">Danh mục không tồn tại</p>
        <p class="text-sm text-stone-500">Danh mục bạn tìm không có trong hệ thống. Hãy chọn danh mục khác hoặc xem toàn bộ mỹ phẩm.</p>
        <a href="{{ route('products.index') }}" data-filter-link class="inline-block bg-sage-500 hover:bg-sage-600 text-white text-sm font-semibold px-6 py-2.5 rounded-full transition">
            Xem tất cả sản phẩm
        </a>
    </div>
@elseif($products->isEmpty())
    <div class="bg-white rounded-3xl border border-sage-100 p-16 text-center space-y-4">
        <p class="font-serif text-xl font-bold text-stone-700">Không tìm thấy sản phẩm phù hợp với bộ lọc</p>
        <p class="text-sm text-stone-500">Hãy thử thay đổi từ khóa tìm kiếm hoặc đặt lại bộ lọc để xem toàn bộ mỹ phẩm.</p>
        <a href="{{ route('products.index') }}" data-filter-link class="inline-block bg-sage-500 hover:bg-sage-600 text-white text-sm font-semibold px-6 py-2.5 rounded-full transition">
            Xem tất cả sản phẩm
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($products as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>

    @if($products->hasPages())
        <nav class="flex flex-wrap items-center justify-center gap-2 pt-4" aria-label="Phân trang">
            @if($products->onFirstPage())
                <span class="px-3.5 py-2 text-xs rounded-full border border-stone-200 text-stone-300">←</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" data-filter-link class="px-3.5 py-2 text-xs rounded-full border border-sage-200 text-sage-700 hover:bg-sage-50">←</a>
            @endif

            @foreach($products->getUrlRange(1, $products->lastPage()) as $pageNum => $url)
                @if($pageNum === $products->currentPage())
                    <span class="px-3.5 py-2 text-xs rounded-full bg-sage-500 text-white font-bold" aria-current="page">{{ $pageNum }}</span>
                @else
                    <a href="{{ $url }}" data-filter-link class="px-3.5 py-2 text-xs rounded-full border border-sage-200 text-sage-700 hover:bg-sage-50">{{ $pageNum }}</a>
                @endif
            @endforeach

            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" data-filter-link class="px-3.5 py-2 text-xs rounded-full border border-sage-200 text-sage-700 hover:bg-sage-50">→</a>
            @else
                <span class="px-3.5 py-2 text-xs rounded-full border border-stone-200 text-stone-300">→</span>
            @endif
        </nav>
    @endif
@endif
