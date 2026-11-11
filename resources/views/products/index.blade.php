@extends('layouts.app')

@section('title', 'Danh Mục Mỹ Phẩm & Chăm Sóc Da Chính Hãng | Shine Beauty')
@section('meta_description', 'Khám phá bộ sưu tập mỹ phẩm, serum, kem dưỡng ẩm, chống nắng, trang điểm và nước hoa chính hãng tại Shine Beauty. Lọc nhanh theo loại da, thương hiệu và khoảng giá.')
@section('canonical', route('products.index'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-sage-100/70 via-sage-50/50 to-[#F5FAF7] rounded-3xl p-8 sm:p-10 border border-sage-100">
        <span class="text-xs font-bold uppercase tracking-[0.2em] text-sage-500">Bộ Sưu Tập Chính Hãng</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-800 mt-1">Tất Cả Sản Phẩm Mỹ Phẩm & Chăm Sóc Da</h1>
        <p class="text-sm text-stone-600 mt-2 max-w-2xl">
            Sử dụng bộ lọc thông minh theo danh mục, thương hiệu, loại da (Da dầu, Da khô, Da nhạy cảm) và khoảng giá để tìm sản phẩm phù hợp nhất.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar Filter Form (hoạt động cả khi tắt JS: submit GET thông thường) -->
        <aside class="lg:col-span-3">
            <form id="product-filter-form" action="{{ route('products.index') }}" method="GET" class="bg-white p-6 rounded-3xl border border-sage-100 shadow-xs space-y-6 sticky top-28">
                <div class="flex items-center justify-between pb-4 border-b border-sage-100">
                    <span class="font-serif font-bold text-stone-800">Bộ Lọc Tìm Kiếm</span>
                    <a href="{{ route('products.index') }}" data-filter-reset class="text-xs text-sage-600 hover:underline font-medium">Đặt lại</a>
                </div>

                <!-- Search Input -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Tìm theo tên / Hoạt chất</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}" maxlength="100" placeholder="VD: Niacinamide, BHA, Son..." class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:outline-none focus:ring-2 focus:ring-sage-300">
                </div>

                <!-- Category Filter (slug) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Danh mục mỹ phẩm</label>
                    <select name="category" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7]">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ $filters['category'] === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter (slug) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Thương hiệu</label>
                    <select name="brand" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7]">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach($brands as $b)
                            @php $brandSlug = \Illuminate\Support\Str::slug($b->name); @endphp
                            <option value="{{ $brandSlug }}" {{ $filters['brand'] === $brandSlug ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->origin }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Skin Type Filter -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Loại da phù hợp</label>
                    @php $skinOptions = ['Da dầu', 'Da khô', 'Da nhạy cảm', 'Mọi loại da']; @endphp
                    <select name="skin_type" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7]">
                        <option value="">Tất cả</option>
                        @foreach($skinOptions as $skin)
                            <option value="{{ $skin }}" {{ $filters['skin_type'] === $skin ? 'selected' : '' }}>{{ $skin }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range Filter (min_price / max_price) -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Khoảng giá (₫)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" min="0" step="10000" value="{{ $filters['min_price'] }}" placeholder="Từ" class="w-full px-3 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7]">
                        <input type="number" name="max_price" min="0" step="10000" value="{{ $filters['max_price'] }}" placeholder="Đến" class="w-full px-3 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7]">
                    </div>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Sắp xếp</label>
                    <select name="sort" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7]">
                        <option value="newest" {{ $filters['sort'] === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ $filters['sort'] === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ $filters['sort'] === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="name_asc" {{ $filters['sort'] === 'name_asc' ? 'selected' : '' }}>Tên A → Z</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3 rounded-full text-sm shadow-md shadow-sage-200 transition">
                    Áp Dụng Bộ Lọc
                </button>
            </form>
        </aside>

        <!-- Products Grid -->
        <div id="product-results" class="lg:col-span-9 space-y-6 transition-opacity duration-150" aria-live="polite">
            @include('products._results')
        </div>
    </div>
</div>

<script>
/**
 * Đồng bộ bộ lọc sản phẩm <-> URL Query Parameters (không reload trang).
 * - URL là nguồn sự thật duy nhất: form được khởi tạo/đồng bộ từ URL.
 * - Thay đổi bộ lọc → history.pushState + fetch khối kết quả (X-Partial: products).
 * - Back/Forward → popstate → đồng bộ form + tải lại kết quả.
 * - Giữ nguyên các query param không thuộc bộ lọc và hash (#...).
 */
(function () {
    const form = document.getElementById('product-filter-form');
    const results = document.getElementById('product-results');
    if (!form || !results || !window.history || !window.fetch) return;

    const FILTER_KEYS = ['search', 'category', 'brand', 'skin_type', 'min_price', 'max_price', 'sort'];
    const LEGACY_KEYS = ['price_range'];
    const DEFAULTS = { sort: 'newest' };
    let lastLoadedQuery = normalizeQuery(new URLSearchParams(window.location.search));
    let controller = null;
    let debounceTimer = null;

    function normalizeQuery(params) {
        const copy = new URLSearchParams(params);
        copy.sort();
        return copy.toString();
    }

    // Đọc form → URL mới (giữ param lạ + hash, reset page khi bộ lọc đổi)
    function buildUrlFromForm(resetPage = true) {
        const url = new URL(window.location.href);
        const params = url.searchParams;
        LEGACY_KEYS.forEach(k => params.delete(k));
        FILTER_KEYS.forEach(key => {
            const el = form.elements[key];
            const value = el ? String(el.value).trim() : '';
            if (value === '' || value === DEFAULTS[key]) {
                params.delete(key);
            } else {
                params.set(key, value);
            }
        });
        if (resetPage) params.delete('page');
        return url;
    }

    // URL → form (khi Back/Forward)
    function syncFormFromUrl() {
        const params = new URLSearchParams(window.location.search);
        FILTER_KEYS.forEach(key => {
            const el = form.elements[key];
            if (!el) return;
            const value = params.get(key) ?? DEFAULTS[key] ?? '';
            if (el.tagName === 'SELECT') {
                const exists = Array.from(el.options).some(o => o.value === value);
                el.value = exists ? value : (DEFAULTS[key] ?? '');
            } else {
                el.value = value;
            }
        });
    }

    async function loadResults(url) {
        const query = normalizeQuery(url.searchParams);
        if (query === lastLoadedQuery) return; // Tránh gọi API trùng lặp
        lastLoadedQuery = query;

        if (controller) controller.abort(); // Hủy request cũ còn đang chạy
        controller = new AbortController();

        results.classList.add('opacity-50', 'pointer-events-none');
        results.setAttribute('aria-busy', 'true');
        try {
            const res = await fetch(url.pathname + url.search, {
                headers: { 'X-Partial': 'products', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                signal: controller.signal,
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            results.innerHTML = await res.text();
            if (typeof window.__applyI18n === 'function') window.__applyI18n();
        } catch (err) {
            if (err.name === 'AbortError') return;
            lastLoadedQuery = null; // Cho phép thử lại
            results.innerHTML = '<div class="bg-white rounded-3xl border border-red-200 p-12 text-center space-y-3">'
                + '<p class="font-serif text-lg font-bold text-stone-700">Không thể tải danh sách sản phẩm</p>'
                + '<p class="text-sm text-stone-500">Vui lòng kiểm tra kết nối và thử lại.</p>'
                + '<button type="button" data-filter-retry class="bg-sage-500 hover:bg-sage-600 text-white text-sm font-semibold px-6 py-2.5 rounded-full">Thử lại</button></div>';
        } finally {
            results.classList.remove('opacity-50', 'pointer-events-none');
            results.removeAttribute('aria-busy');
        }
    }

    function navigate(url, { scroll = false } = {}) {
        if (url.href !== window.location.href) {
            window.history.pushState({ productFilters: true }, '', url);
        }
        loadResults(url);
        if (scroll) {
            const top = results.getBoundingClientRect().top + window.scrollY - 120;
            window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
        }
    }

    // Select thay đổi → áp dụng ngay
    form.addEventListener('change', e => {
        if (e.target.tagName === 'SELECT') navigate(buildUrlFromForm());
    });

    // Ô tìm kiếm / giá → debounce để hạn chế gọi API
    form.addEventListener('input', e => {
        if (e.target.tagName !== 'INPUT') return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => navigate(buildUrlFromForm()), 450);
    });

    form.addEventListener('submit', e => {
        e.preventDefault();
        clearTimeout(debounceTimer);
        navigate(buildUrlFromForm());
    });

    // Nút "Đặt lại": xóa bộ lọc, giữ param lạ + hash
    form.querySelector('[data-filter-reset]')?.addEventListener('click', e => {
        e.preventDefault();
        form.reset();
        FILTER_KEYS.forEach(k => { if (form.elements[k]) form.elements[k].value = DEFAULTS[k] ?? ''; });
        navigate(buildUrlFromForm());
    });

    // Link phân trang / "Xem tất cả" trong khối kết quả (event delegation)
    results.addEventListener('click', e => {
        if (e.target.closest('[data-filter-retry]')) {
            loadResults(new URL(window.location.href));
            return;
        }
        const link = e.target.closest('a[data-filter-link]');
        if (!link || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
        e.preventDefault();
        const url = new URL(link.href);
        url.hash = window.location.hash;
        navigate(url, { scroll: true });
        syncFormFromUrl();
    });

    // Back / Forward
    window.addEventListener('popstate', () => {
        syncFormFromUrl();
        loadResults(new URL(window.location.href));
    });
})();
</script>
@endsection
