@php
    $finalPrice = $product->sale_price ?? $product->price;
    $discountPercent = ($product->sale_price && $product->sale_price < $product->price)
        ? round((($product->price - $product->sale_price) / $product->price) * 100)
        : null;
    $isFavorited = auth()->check() && in_array((int) $product->id, $wishlistProductIds ?? [], true);
@endphp

<div class="group bg-white rounded-3xl border border-sage-100/90 overflow-hidden shadow-xs hover:shadow-xl hover:shadow-sage-100/60 transition-all duration-300 flex flex-col justify-between">
    <div>
        <!-- Image Wrapper -->
        <div class="relative aspect-square overflow-hidden bg-sage-50/50">
            <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
                <img src="{{ $product->first_image }}"
                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80';"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </a>

            <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                @if($product->is_best_seller)
                    <span class="inline-flex items-center gap-1 bg-sage-500 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-xs">
                        ✦ Best Seller
                    </span>
                @endif
            </div>

            @if($discountPercent)
                <span class="absolute top-3 right-3 inline-block bg-terracotta-600 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full w-fit shadow-xs border border-terracotta-500 pointer-events-none">
                    -{{ $discountPercent }}%
                </span>
            @endif

            <span class="absolute bottom-3 left-3 bg-white/90 text-sage-700 text-[11px] font-medium px-3 py-1 rounded-full border border-sage-100 shadow-2xs pointer-events-none">
                {{ $product->skin_type }}
            </span>

            <!-- Nút Trái Tim Yêu Thích Sản Phẩm (Sáng lên khi đã yêu thích, mờ đi khi chưa yêu thích) -->
            <button type="button"
                data-wishlist-btn="{{ $product->id }}"
                data-favorited="{{ $isFavorited ? '1' : '0' }}"
                onclick="toggleWishlistProduct({{ $product->id }}, this)"
                title="{{ $isFavorited ? 'Bỏ yêu thích sản phẩm này' : 'Thêm vào danh mục Yêu Thích' }}"
                class="absolute bottom-3 right-3 z-10 w-9 h-9 rounded-full flex items-center justify-center border transition-all duration-200 cursor-pointer {{ $isFavorited ? 'opacity-100 bg-red-50 text-red-500 border-red-300 shadow-md shadow-red-200/80 ring-2 ring-red-400/50 scale-105' : 'opacity-40 hover:opacity-95 bg-white/80 text-stone-400 hover:text-red-500 border-stone-200/80' }}">
                <svg class="w-5 h-5 transition-transform duration-200" viewBox="0 0 24 24" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-5">
            <div class="flex items-center justify-between text-xs text-stone-400 mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">
                        {{ $product->product_code }}
                    </span>
                    <span class="font-semibold uppercase tracking-wider text-sage-500">
                        {{ $product->brand->name ?? 'Shine' }}
                    </span>
                </div>
                <span>{{ $product->volume }}</span>
            </div>

            <a href="{{ route('products.show', $product->slug) }}" class="font-serif font-bold text-stone-800 group-hover:text-sage-600 transition line-clamp-2 text-base leading-snug min-h-[2.75rem] block">
                {{ $product->name }}
            </a>

            <div class="flex items-center justify-between mt-2 text-xs">
                <span class="text-amber-400">★★★★★ <span class="text-stone-400 ml-0.5">(4.9)</span></span>
                <span class="font-semibold {{ $product->stock > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    Kho: {{ $product->stock }} SP
                </span>
            </div>
        </div>
    </div>

    <!-- Price & Add to Cart -->
    <div class="px-5 pb-5 pt-2 flex items-center justify-between border-t border-sage-50">
        <div>
            <div class="text-lg font-bold text-sage-600">
                {{ number_format($finalPrice, 0, ',', '.') }}₫
            </div>
            @if($product->sale_price && $product->sale_price < $product->price)
                <div class="text-xs text-stone-400 line-through">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </div>
            @endif
        </div>

        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="inline-flex items-center gap-1.5 bg-sage-50 hover:bg-sage-500 text-sage-700 hover:text-white font-medium text-xs px-3.5 py-2.5 rounded-full transition-colors duration-200 border border-sage-200/70">
                <span>+ Thêm giỏ</span>
            </button>
        </form>
    </div>
</div>
