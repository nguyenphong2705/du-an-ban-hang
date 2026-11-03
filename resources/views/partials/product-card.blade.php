@php
    $finalPrice = $product->sale_price ?? $product->price;
    $discountPercent = ($product->sale_price && $product->sale_price < $product->price)
        ? round((($product->price - $product->sale_price) / $product->price) * 100)
        : null;
@endphp

<div class="group bg-white rounded-3xl border border-rose-100/90 overflow-hidden shadow-xs hover:shadow-xl hover:shadow-rose-100/60 transition-all duration-300 flex flex-col justify-between">
    <div>
        <!-- Image Wrapper -->
        <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-square overflow-hidden bg-rose-50/50">
            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                @if($product->is_best_seller)
                    <span class="inline-flex items-center gap-1 bg-rose-500 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-xs">
                        ✦ Best Seller
                    </span>
                @endif
                @if($discountPercent)
                    <span class="inline-block bg-stone-900/80 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full w-fit">
                        -{{ $discountPercent }}%
                    </span>
                @endif
            </div>

            <span class="absolute bottom-3 left-3 bg-white/90 text-rose-700 text-[11px] font-medium px-3 py-1 rounded-full border border-rose-100 shadow-2xs">
                {{ $product->skin_type }}
            </span>
        </a>

        <!-- Content -->
        <div class="p-5">
            <div class="flex items-center justify-between text-xs text-stone-400 mb-1.5">
                <span class="font-semibold uppercase tracking-wider text-rose-500">
                    {{ $product->brand->name ?? 'Lumina' }}
                </span>
                <span>{{ $product->volume }}</span>
            </div>

            <a href="{{ route('products.show', $product->slug) }}" class="font-serif font-bold text-stone-800 group-hover:text-rose-600 transition line-clamp-2 text-base leading-snug min-h-[2.75rem] block">
                {{ $product->name }}
            </a>

            <div class="flex items-center gap-1 mt-2 text-amber-400 text-xs">
                ★★★★★ <span class="text-stone-400 ml-1">(4.9)</span>
            </div>
        </div>
    </div>

    <!-- Price & Add to Cart -->
    <div class="px-5 pb-5 pt-2 flex items-center justify-between border-t border-rose-50">
        <div>
            <div class="text-lg font-bold text-rose-600">
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
            <button type="submit" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-500 text-rose-700 hover:text-white font-medium text-xs px-3.5 py-2.5 rounded-full transition-colors duration-200 border border-rose-200/70">
                <span>+ Thêm giỏ</span>
            </button>
        </form>
    </div>
</div>
