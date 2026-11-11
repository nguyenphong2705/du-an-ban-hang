@extends('layouts.app')

@section('title', 'Shine Beauty | Mỹ Phẩm & Chăm Sóc Da Cao Cấp Chính Hãng')
@section('meta_description', 'Shine Beauty — Hệ thống phân phối mỹ phẩm cao cấp & chăm sóc da chuẩn khoa học chính hãng từ Pháp, Hàn Quốc và Nhật Bản. Miễn phí vận chuyển toàn quốc cho đơn từ 500.000₫.')
@section('canonical', route('home'))

@section('content')
<div class="space-y-20">
    <!-- 1. HERO BANNER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-sage-100/80 via-[#F5FAF7] to-[#ECF5F0] border-b border-sage-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Column -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 bg-white/90 border border-sage-200 text-sage-600 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide shadow-2xs">
                        <span>✦ BỘ SƯU TẬP DƯỠNG DA 2026 — GLOW & RADIANCE</span>
                    </div>

                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-stone-900 leading-[1.15]">
                        Đánh Thức Vẻ Đẹp <br>
                        <span class="text-sage-500 italic font-normal">Thuần Khiết & Rạng Ngời</span>
                    </h1>

                    <p class="text-stone-600 text-base sm:text-lg leading-relaxed max-w-2xl">
                        Khám phá quy trình chăm sóc da chuẩn khoa học với Niacinamide 10%, BHA 2%, Retinol 0.5% tinh khiết và Hyaluronic Acid đa tầng từ những thương hiệu danh tiếng hàng đầu Pháp, Hàn Quốc và Nhật Bản.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2.5 bg-sage-500 hover:bg-sage-600 text-white font-semibold px-8 py-4 rounded-full shadow-lg shadow-sage-200/90 transition transform hover:-translate-y-0.5">
                            <span>Khám Phá Bộ Sưu Tập →</span>
                        </a>
                        <a href="#skin-explorer" class="inline-flex items-center gap-2 bg-white hover:bg-sage-50 text-stone-800 font-semibold px-7 py-4 rounded-full border border-sage-200 transition">
                            <span>Chọn Mỹ Phẩm Theo Loại Da</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-sage-200/60 max-w-lg text-xs text-stone-600">
                        <div>✓ 100% Chính hãng</div>
                        <div>✓ Kiểm nghiệm da liễu</div>
                        <div>✓ Đổi trả 14 ngày</div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl shadow-sage-200/80 border-8 border-white aspect-[4/5]">
                        <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1000&q=85" alt="Shine Beauty Skincare Collection" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-900/50 via-transparent to-transparent flex items-end p-8">
                            <div class="bg-white/95 backdrop-blur-md p-5 rounded-2xl w-full shadow-lg">
                                <p class="text-xs font-bold uppercase tracking-wider text-sage-500">Best Seller Tuần Này</p>
                                <p class="font-serif font-bold text-stone-800 text-base mt-0.5">Serum Hyaluronic Acid 2% + B5 Cấp Ẩm Đa Tầng</p>
                                <p class="text-xs text-stone-500 mt-1">Phục hồi màng ẩm tự nhiên chỉ sau 7 ngày</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. DANH MỤC NỔI BẬT -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-sage-500">Danh Mục Làm Đẹp</span>
                <h2 class="font-serif text-3xl font-bold text-stone-800 mt-1">Khám Phá Theo Danh Mục</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-sage-600 hover:text-sage-700 mt-2 sm:mt-0">
                Xem tất cả danh mục →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="group relative h-72 rounded-3xl overflow-hidden shadow-sm border border-sage-100">
                    <img src="{{ $cat->image }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-stone-950/75 via-stone-900/20 to-transparent flex flex-col justify-end p-6">
                        <h3 class="font-serif text-2xl font-bold text-white group-hover:text-sage-200 transition">{{ $cat->name }}</h3>
                        <span class="text-xs font-medium text-sage-200 mt-1">Khám phá ({{ $cat->products_count }} sản phẩm) →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 3. SẢN PHẨM BÁN CHẠY (BEST SELLERS) -->
    <section class="bg-gradient-to-b from-sage-50/60 to-[#F5FAF7] py-16 border-y border-sage-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-sage-500">Được Yêu Thích Nhất</span>
                    <h2 class="font-serif text-3xl font-bold text-stone-800 mt-1">Sản Phẩm Bán Chạy (Best Sellers)</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-sage-600 hover:text-sage-700 mt-2 sm:mt-0">
                    Xem toàn bộ {{ $allProducts->count() }} sản phẩm →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($bestSellers as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. LỌC NHANH THEO LOẠI DA & DANH SÁCH SẢN PHẨM PHÂN TRANG -->
    <section id="skin-explorer" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-sage-500">Cá Nhân Hóa Chu Trình Dưỡng Da</span>
            <h2 class="font-serif text-3xl font-bold text-stone-800 mt-1">Chọn Mỹ Phẩm Theo Loại Da Của Bạn</h2>
            <p class="text-sm text-stone-500 mt-2">
                Mỗi làn da có một nhu cầu riêng biệt. Hãy chọn tình trạng da để Shine Beauty gợi ý công thức hoạt chất phù hợp nhất.
            </p>
        </div>

        @php
            $skinTabs = [
                ['key' => 'Tất cả', 'label' => 'Tất cả loại da', 'desc' => 'Bộ sưu tập chăm sóc toàn diện'],
                ['key' => 'Da dầu', 'label' => 'Da dầu & Hỗn hợp', 'desc' => 'Kiểm soát bã nhờn, thu nhỏ lỗ chân lông (Niacinamide, BHA)'],
                ['key' => 'Da khô', 'label' => 'Da khô & Thiếu ẩm', 'desc' => 'Cấp ẩm đa tầng, khóa ẩm chuyên sâu (Hyaluronic Acid, Nhân sâm)'],
                ['key' => 'Da nhạy cảm', 'label' => 'Da nhạy cảm', 'desc' => 'Làm dịu kích ứng, phục hồi màng ẩm (Vitamin B5, Rau má)'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach($skinTabs as $tab)
                @php $active = ($selectedSkin === $tab['key']); @endphp
                <a href="{{ route('home', ['skin_type' => $tab['key']]) }}#skin-explorer"
                   class="text-left p-5 rounded-3xl border transition-all duration-200 block {{ $active ? 'bg-sage-500 text-white border-sage-500 shadow-lg shadow-sage-200' : 'bg-white hover:bg-sage-50/60 text-stone-800 border-sage-100' }}">
                    <span class="font-serif font-bold text-base block mb-1">{{ $tab['label'] }}</span>
                    <p class="text-xs leading-relaxed {{ $active ? 'text-sage-50' : 'text-stone-500' }}">{{ $tab['desc'] }}</p>
                </a>
            @endforeach
        </div>

        @if($skinProducts->isEmpty())
            <div class="bg-white rounded-3xl border border-sage-100 p-12 text-center space-y-3">
                <p class="font-serif text-lg font-bold text-stone-700">Chưa có sản phẩm phù hợp</p>
                <p class="text-sm text-stone-500">Vui lòng chọn loại da khác hoặc xem lại tất cả sản phẩm.</p>
                <a href="{{ route('home', ['skin_type' => 'Tất cả']) }}#skin-explorer" class="inline-block bg-sage-500 hover:bg-sage-600 text-white text-xs font-semibold px-5 py-2.5 rounded-full transition">
                    Xem tất cả sản phẩm
                </a>
            </div>
        @else
            <!-- Thông tin số lượng sản phẩm -->
            <div class="flex items-center justify-between text-xs text-stone-500 mb-4 px-1">
                <span>Hiển thị {{ $skinProducts->firstItem() ?? 0 }} - {{ $skinProducts->lastItem() ?? 0 }} trong tổng số <strong class="text-sage-700">{{ $skinProducts->total() }}</strong> sản phẩm</span>
                <span>Trang {{ $skinProducts->currentPage() }} / {{ $skinProducts->lastPage() }}</span>
            </div>

            <!-- Lưới hiển thị: 4 cột x 2 hàng = 8 sản phẩm mỗi trang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($skinProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <!-- Thanh phân trang (Pagination) -->
            @if($skinProducts->hasPages())
                <div class="pt-10 flex flex-col items-center gap-3">
                    <nav class="flex flex-wrap items-center justify-center gap-2" aria-label="Phân trang sản phẩm">
                        {{-- Nút trang trước --}}
                        @if($skinProducts->onFirstPage())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs sm:text-sm font-medium rounded-full border border-stone-200 text-stone-300 cursor-not-allowed select-none bg-stone-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span>Trước</span>
                            </span>
                        @else
                            <a href="{{ $skinProducts->previousPageUrl() }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs sm:text-sm font-medium rounded-full border border-sage-200 text-sage-700 bg-white hover:bg-sage-50 hover:border-sage-300 transition shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span>Trước</span>
                            </a>
                        @endif

                        {{-- Danh sách các số trang --}}
                        @foreach($skinProducts->getUrlRange(1, $skinProducts->lastPage()) as $pageNum => $url)
                            @if($pageNum === $skinProducts->currentPage())
                                <span class="w-10 h-10 flex items-center justify-center text-xs sm:text-sm rounded-full bg-sage-500 text-white font-bold shadow-md shadow-sage-200 select-none" aria-current="page">
                                    {{ $pageNum }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center text-xs sm:text-sm rounded-full border border-sage-200 text-stone-700 bg-white hover:bg-sage-50 hover:text-sage-700 hover:border-sage-300 transition shadow-2xs font-medium">
                                    {{ $pageNum }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Nút trang sau --}}
                        @if($skinProducts->hasMorePages())
                            <a href="{{ $skinProducts->nextPageUrl() }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs sm:text-sm font-medium rounded-full border border-sage-200 text-sage-700 bg-white hover:bg-sage-50 hover:border-sage-300 transition shadow-2xs">
                                <span>Tiếp</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs sm:text-sm font-medium rounded-full border border-stone-200 text-stone-300 cursor-not-allowed select-none bg-stone-50">
                                <span>Tiếp</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        @endif
    </section>

    <!-- 5. THƯƠNG HIỆU ĐỒNG HÀNH -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-sage-100 p-8 sm:p-10 shadow-xs">
            <div class="text-center mb-8">
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-sage-500">Đối Tác Chính Hãng</span>
                <h3 class="font-serif text-2xl font-bold text-stone-800 mt-1">Thương Hiệu Mỹ Phẩm Hàng Đầu Thế Giới</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($brands as $brand)
                    <a href="{{ route('products.index', ['brand' => $brand->name]) }}" class="flex flex-col items-center justify-center p-6 rounded-2xl bg-[#F5FAF7] hover:bg-sage-50/70 border border-sage-100/80 transition group">
                        <span class="font-serif text-xl font-bold text-stone-800 group-hover:text-sage-600">{{ $brand->name }}</span>
                        <span class="text-xs text-stone-400 mt-1">Xuất xứ: {{ $brand->origin }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
