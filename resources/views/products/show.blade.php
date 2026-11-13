@extends('layouts.app')

@php
    $finalPrice = $product->sale_price ?? $product->price;
    $discountPercent = ($product->sale_price && $product->sale_price < $product->price)
        ? round((($product->price - $product->sale_price) / $product->price) * 100)
        : null;
    $ingredientsList = array_filter(array_map('trim', explode(',', (string) $product->ingredients)));
    $seoDescription = \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 155, '...');
@endphp

@section('title', $product->name . ' - Chính Hãng ' . ($product->brand->name ?? 'SHINE Beauty') . ' | SHINE Beauty')
@section('meta_description', $seoDescription ?: ($product->name . ' chính hãng tại SHINE Beauty. Dành cho ' . $product->skin_type . ', dung tích ' . $product->volume . '.'))
@section('meta_keywords', implode(', ', array_filter([$product->name, $product->brand->name ?? '', $product->category->name ?? '', $product->skin_type, 'mỹ phẩm chính hãng', 'SHINE Beauty'])))
@section('canonical', route('products.show', $product->slug))
@section('og_type', 'product')
@section('og_image', $product->first_image)

@push('structured_data')
<!-- Dữ liệu có cấu trúc Schema.org Product & BreadcrumbList chuẩn Google Rich Snippets -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@@type": "ListItem",
                    "position": 1,
                    "name": "Trang chủ",
                    "item": "{{ route('home') }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 2,
                    "name": "{{ $product->category->name }}",
                    "item": "{{ route('products.index', ['category' => $product->category->slug]) }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 3,
                    "name": "{{ $product->name }}",
                    "item": "{{ route('products.show', $product->slug) }}"
                }
            ]
        },
        {
            "@@type": "Product",
            "name": "{{ $product->name }}",
            "sku": "{{ $product->product_code }}",
            "image": ["{{ $product->first_image }}"],
            "description": "{{ addslashes($seoDescription) }}",
            "brand": {
                "@@type": "Brand",
                "name": "{{ $product->brand->name ?? 'SHINE Beauty' }}"
            },
            "offers": {
                "@@type": "Offer",
                "url": "{{ route('products.show', $product->slug) }}",
                "priceCurrency": "VND",
                "price": "{{ (int) $finalPrice }}",
                "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
                "itemCondition": "https://schema.org/NewCondition"
            },
            "aggregateRating": {
                "@@type": "AggregateRating",
                "ratingValue": "{{ number_format((float) $product->rating, 1, '.', '') }}",
                "reviewCount": "{{ max(1, (int) $product->reviews_count) }}"
            }
        }
    ]
}
</script>
@endpush

@section('content')

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
                    <span class="absolute top-4 left-4 bg-terracotta-600 text-white text-xs font-bold px-3.5 py-1.5 rounded-full shadow-md border border-terracotta-500">
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
                    @if($product->is_best_seller)
                        <span class="inline-flex items-center gap-1 bg-[#D4728C] text-white text-xs font-semibold px-3 py-1 rounded-full shadow-2xs border border-[#C25E79]">
                            ✦ Best Seller
                        </span>
                    @endif
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
            @php
                $isProductFavorited = auth()->check() && in_array((int) $product->id, $wishlistProductIds ?? [], true);
            @endphp
            <form action="{{ route('cart.add') }}" method="POST" class="flex flex-wrap items-center gap-3.5 pt-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="flex items-center border border-sage-200 rounded-full bg-white px-3 py-2">
                    <label for="quantity" class="text-xs text-stone-500 mr-2 pl-2">Số lượng:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="20" class="w-14 text-center font-bold text-sm text-stone-800 focus:outline-none">
                </div>

                <button type="submit" class="flex-1 min-w-[150px] inline-flex items-center justify-center whitespace-nowrap bg-white hover:bg-sage-50 text-sage-600 border-2 border-sage-500 font-semibold py-3.5 px-5 rounded-full transition text-sm cursor-pointer">
                    + Thêm Vào Giỏ
                </button>

                <button type="submit" name="buy_now" value="1" class="flex-1 min-w-[150px] inline-flex items-center justify-center whitespace-nowrap bg-sage-500 hover:bg-sage-600 text-white border-2 border-sage-500 hover:border-sage-600 font-semibold py-3.5 px-5 rounded-full shadow-lg shadow-sage-200 transition text-sm cursor-pointer">
                    Mua Ngay →
                </button>

                <button type="button"
                    data-wishlist-btn="{{ $product->id }}"
                    data-favorited="{{ $isProductFavorited ? '1' : '0' }}"
                    onclick="toggleWishlistProduct({{ $product->id }}, this)"
                    title="{{ $isProductFavorited ? 'Bỏ yêu thích sản phẩm này' : 'Thêm vào danh mục Yêu Thích' }}"
                    class="w-12 h-12 rounded-full flex items-center justify-center border transition-all duration-200 cursor-pointer flex-shrink-0 {{ $isProductFavorited ? 'opacity-100 bg-red-50 text-red-500 border-red-300 shadow-md shadow-red-200/80 ring-2 ring-red-400/50 scale-105' : 'opacity-40 hover:opacity-95 bg-white text-stone-400 hover:text-red-500 border-stone-200' }}">
                    <svg class="w-6 h-6 transition-transform duration-200" viewBox="0 0 24 24" fill="{{ $isProductFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
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
