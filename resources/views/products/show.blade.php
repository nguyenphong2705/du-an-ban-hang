@extends('layouts.app')

@section('title', $product->name . ' | Shine Beauty')

@section('content')
@php
    $finalPrice = $product->sale_price ?? $product->price;
    $discountPercent = ($product->sale_price && $product->sale_price < $product->price)
        ? round((($product->price - $product->sale_price) / $product->price) * 100)
        : null;
    $ingredientsList = array_filter(array_map('trim', explode(',', $product->ingredients)));
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-stone-500">
        <a href="{{ route('home') }}" class="hover:text-sage-600">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-sage-600">
            {{ $product->category->name }}
        </a>
        <span>/</span>
        <span class="text-stone-800 font-medium line-clamp-1">{{ $product->name }}</span>
    </nav>

    <!-- Product Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Gallery -->
        <div class="lg:col-span-6 space-y-4">
            <div class="relative aspect-square rounded-3xl overflow-hidden bg-white border border-sage-100 shadow-sm">
                <img id="main-product-image"
                     src="{{ $product->first_image }}"
                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80';"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover">
                @if($discountPercent)
                    <span class="absolute top-4 left-4 bg-wine-700 text-white text-xs font-bold px-3.5 py-1.5 rounded-full shadow-md border border-wine-600">
                        Tiết kiệm {{ $discountPercent }}%
                    </span>
                @endif
            </div>

            @if(is_array($product->images) && count($product->images) > 1)
                <div class="flex gap-3">
                    @foreach($product->images as $img)
                        <button type="button" onclick="document.getElementById('main-product-image').src='{{ $img }}'" class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-sage-200 hover:border-sage-500 transition">
                            <img src="{{ $img }}"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80';"
                                 alt="Thumbnail"
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-6 space-y-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-mono font-bold bg-stone-900 text-white px-3 py-1 rounded-full">
                        Mã SP: {{ $product->product_code }}
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-600 bg-sage-100/80 px-3 py-1 rounded-full">
                        {{ $product->brand->name }} ({{ $product->brand->origin }})
                    </span>
                    <span class="text-xs font-medium text-stone-600 bg-white border border-sage-200 px-3 py-1 rounded-full">
                        Loại da: {{ $product->skin_type }}
                    </span>
                    <span class="text-xs font-medium text-stone-600 bg-white border border-sage-200 px-3 py-1 rounded-full">
                        Dung tích: {{ $product->volume }}
                    </span>
                </div>

                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900 leading-snug">
                    {{ $product->name }}
                </h1>

                <div class="flex items-center gap-3 text-sm">
                    <span class="text-amber-400">★★★★★</span>
                    <span class="font-semibold text-stone-700">4.9/5</span>
                    <span class="text-stone-400">|</span>
                    <span class="text-emerald-600 font-semibold">Tồn kho tiêu chuẩn: {{ $product->stock }} sản phẩm</span>
                </div>
            </div>

            <!-- Price Box -->
            <div class="bg-sage-50/70 p-5 rounded-2xl border border-sage-100 flex items-baseline gap-4">
                <span class="text-3xl font-bold text-sage-600">
                    {{ number_format($finalPrice, 0, ',', '.') }}₫
                </span>
                @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="text-base text-stone-400 line-through">
                        {{ number_format($product->price, 0, ',', '.') }}₫
                    </span>
                @endif
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500">Công dụng nổi bật</h3>
                <div class="text-sm text-stone-600 leading-relaxed space-y-2 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-stone-800 [&_h3]:text-base [&_h3]:font-bold [&_h3]:text-sage-700 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_img]:rounded-2xl [&_img]:my-3 [&_img]:max-w-full [&_img]:h-auto [&_img]:border [&_img]:border-sage-100">
                    {!! $product->description !!}
                </div>
            </div>

            <!-- Active Ingredients Highlight -->
            <div class="bg-white p-5 rounded-2xl border border-sage-100 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-sage-600">
                    🧪 Bảng Thành Phần Chi Tiết (INCI Ingredients)
                </h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(array_slice($ingredientsList, 0, 6) as $ing)
                        <span class="text-xs bg-sage-50 text-sage-700 px-3 py-1 rounded-full font-medium border border-sage-200/60">
                            {{ $ing }}
                        </span>
                    @endforeach
                </div>
                <p class="text-xs text-stone-500 leading-relaxed pt-1 border-t border-sage-50">
                    <strong>Toàn bộ thành phần:</strong> {{ $product->ingredients }}
                </p>
            </div>

            <!-- Add to Cart / Buy Now Form -->
            <form action="{{ route('cart.add') }}" method="POST" class="flex flex-wrap items-center gap-4 pt-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="flex items-center border border-sage-200 rounded-full bg-white px-3 py-2">
                    <label for="quantity" class="text-xs text-stone-500 mr-2 pl-2">Số lượng:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="20" class="w-14 text-center font-bold text-sm text-stone-800 focus:outline-none">
                </div>

                <button type="submit" class="flex-1 min-w-[180px] bg-white hover:bg-sage-50 text-sage-600 border-2 border-sage-500 font-semibold py-3.5 px-6 rounded-full transition text-sm">
                    + Thêm Vào Giỏ Hàng
                </button>

                <button type="submit" name="buy_now" value="1" class="flex-1 min-w-[180px] bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3.5 px-6 rounded-full shadow-lg shadow-sage-200 transition text-sm">
                    Mua Ngay →
                </button>
            </form>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section class="pt-10 border-t border-sage-100 space-y-6">
            <h2 class="font-serif text-2xl font-bold text-stone-800">Sản Phẩm Cùng Danh Mục</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    @include('partials.product-card', ['product' => $rel])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
