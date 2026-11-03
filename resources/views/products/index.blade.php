@extends('layouts.app')

@section('title', 'Tất Cả Mỹ Phẩm & Dưỡng Da | Lumina Beauty')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-rose-100/70 via-rose-50/50 to-[#FDFBF7] rounded-3xl p-8 sm:p-10 border border-rose-100">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-rose-500">Bộ Sưu Tập Chính Hãng</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-800 mt-1">Tất Cả Sản Phẩm Mỹ Phẩm & Chăm Sóc Da</h1>
        <p class="text-sm text-stone-600 mt-2 max-w-2xl">
            Sử dụng bộ lọc thông minh theo danh mục, thương hiệu, loại da (Da dầu, Da khô, Da nhạy cảm) và khoảng giá để tìm sản phẩm phù hợp nhất.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar Filter Form -->
        <aside class="lg:col-span-3">
            <form action="{{ route('products.index') }}" method="GET" class="bg-white p-6 rounded-3xl border border-rose-100 shadow-xs space-y-6 sticky top-28">
                <div class="flex items-center justify-between pb-4 border-b border-rose-100">
                    <span class="font-serif font-bold text-stone-800">Bộ Lọc Tìm Kiếm</span>
                    <a href="{{ route('products.index') }}" class="text-xs text-rose-600 hover:underline font-medium">Đặt lại</a>
                </div>

                <!-- Search Input -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Tìm theo tên / Hoạt chất</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="VD: Niacinamide, BHA, Son..." class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300">
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Danh mục mỹ phẩm</label>
                    <select name="category" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7]">
                        <option value="ALL">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Thương hiệu</label>
                    <select name="brand" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7]">
                        <option value="ALL">Tất cả thương hiệu</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->name }}" {{ request('brand') === $b->name ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->origin }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Skin Type Filter -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Loại da phù hợp</label>
                    @php
                        $skinOptions = ['Tất cả', 'Da dầu', 'Da khô', 'Da nhạy cảm', 'Mọi loại da'];
                        $currentSkin = request('skin_type', 'Tất cả');
                    @endphp
                    <select name="skin_type" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7]">
                        @foreach($skinOptions as $skin)
                            <option value="{{ $skin }}" {{ $currentSkin === $skin ? 'selected' : '' }}>{{ $skin }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range Filter -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Khoảng giá</label>
                    @php $currentPrice = request('price_range', 'ALL'); @endphp
                    <select name="price_range" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7]">
                        <option value="ALL" {{ $currentPrice === 'ALL' ? 'selected' : '' }}>Tất cả mức giá</option>
                        <option value="UNDER_500" {{ $currentPrice === 'UNDER_500' ? 'selected' : '' }}>Dưới 500.000₫</option>
                        <option value="500_TO_1000" {{ $currentPrice === '500_TO_1000' ? 'selected' : '' }}>Từ 500.000₫ - 1.000.000₫</option>
                        <option value="ABOVE_1000" {{ $currentPrice === 'ABOVE_1000' ? 'selected' : '' }}>Trên 1.000.000₫</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3 rounded-full text-sm shadow-md shadow-rose-200 transition">
                    Áp Dụng Bộ Lọc
                </button>
            </form>
        </aside>

        <!-- Products Grid -->
        <div class="lg:col-span-9 space-y-6">
            <div class="bg-white px-6 py-4 rounded-2xl border border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <p class="text-sm text-stone-600">
                    Hiển thị <span class="font-bold text-rose-600">{{ $products->count() }}</span> sản phẩm phù hợp
                </p>
                @if(request('skin_type') && request('skin_type') !== 'Tất cả')
                    <span class="text-xs bg-rose-50 text-rose-700 px-3 py-1 rounded-full font-medium">
                        Loại da: {{ request('skin_type') }}
                    </span>
                @endif
            </div>

            @if($products->isEmpty())
                <div class="bg-white rounded-3xl border border-rose-100 p-16 text-center space-y-4">
                    <p class="font-serif text-xl font-bold text-stone-700">Không tìm thấy sản phẩm phù hợp với bộ lọc</p>
                    <p class="text-sm text-stone-500">Hãy thử thay đổi từ khóa tìm kiếm hoặc đặt lại bộ lọc để xem toàn bộ mỹ phẩm.</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-6 py-2.5 rounded-full transition">
                        Xem tất cả sản phẩm
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
