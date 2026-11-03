<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Lumina Beauty | Mỹ Phẩm & Chăm Sóc Da Cao Cấp Chính Hãng'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                        serif: ['Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body, h1, h2, h3, h4, h5, h6, input, button, select, textarea {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col bg-[#FDFBF7] text-stone-800">
    @php
        $cart = session('cart', []);
        $cartCount = collect($cart)->sum('quantity');
        $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
    @endphp

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-rose-200/80 via-rose-100 to-rose-200/80 text-rose-900 text-xs py-2 px-4 text-center font-medium tracking-wide">
        {{ __('✨ Lumina Beauty Laravel 11 Edition — Miễn phí vận chuyển toàn quốc cho đơn từ 500.000₫ — Cam kết 100% Mỹ phẩm Chính hãng ✨') }}
    </div>

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 bg-[#FDFBF7]/95 backdrop-blur-md border-b border-rose-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-rose-500 to-rose-300 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition">
                        <span class="text-lg">✦</span>
                    </div>
                    <div>
                        <span class="font-serif text-2xl font-bold tracking-tight text-stone-800">
                            Lumina <span class="text-rose-500 font-normal italic">Beauty</span>
                        </span>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-stone-400 -mt-1">
                            Laravel 11 Luxury Cosmetics
                        </p>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-5 text-sm font-medium">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-rose-600 font-semibold' : 'text-stone-700 hover:text-rose-500' }}">{{ __('Trang chủ') }}</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') && !request('category') ? 'text-rose-600 font-semibold' : 'text-stone-700 hover:text-rose-500' }}">{{ __('Sản phẩm') }}</a>
                    <a href="{{ route('products.index', ['category' => 'cham-soc-da']) }}" class="{{ request('category') === 'cham-soc-da' ? 'text-rose-600 font-semibold' : 'text-stone-700 hover:text-rose-500' }}">{{ __('Chăm sóc da') }}</a>
                    <a href="{{ route('orders.track') }}" class="{{ request()->routeIs('orders.track') ? 'text-rose-600 font-semibold' : 'text-stone-700 hover:text-rose-500' }}">{{ __('Tra cứu SĐT') }}</a>

                    @if(auth()->check() && !auth()->user()->is_admin)
                        <a href="{{ route('customer.account') }}" class="text-xs font-bold bg-rose-500 hover:bg-rose-600 text-white px-3.5 py-2 rounded-full transition shadow-xs">
                            👤 {{ auth()->user()->username ?? auth()->user()->name }} ({{ __('Đơn & Voucher') }})
                        </a>
                    @else
                        <a href="{{ route('customer.login') }}" class="text-xs font-bold bg-rose-100 hover:bg-rose-200 text-rose-800 px-3.5 py-2 rounded-full transition border border-rose-200">
                            👤 {{ __('Đăng nhập Khách hàng') }}
                        </a>
                    @endif
                </nav>

                <!-- Language Switcher & Cart Button -->
                <div class="flex items-center gap-2.5">
                    <div class="inline-flex items-center rounded-full bg-white border border-rose-200 p-0.5 shadow-2xs text-xs font-bold">
                        <a href="{{ route('lang.switch', 'vi') }}"
                           title="Tiếng Việt (Mặc định)"
                           class="px-2.5 py-1.5 rounded-full transition flex items-center gap-1 {{ app()->getLocale() === 'vi' ? 'bg-rose-500 text-white shadow-2xs' : 'text-stone-600 hover:text-rose-600' }}">
                            <span>🇻🇳</span>
                            <span>VI</span>
                        </a>
                        <a href="{{ route('lang.switch', 'en') }}"
                           title="Switch to English"
                           class="px-2.5 py-1.5 rounded-full transition flex items-center gap-1 {{ app()->getLocale() === 'en' ? 'bg-rose-500 text-white shadow-2xs' : 'text-stone-600 hover:text-rose-600' }}">
                            <span>🇬🇧</span>
                            <span>EN</span>
                        </a>
                    </div>

                    <button type="button" onclick="document.getElementById('cart-drawer').classList.remove('hidden')" class="relative flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 px-4 py-2.5 rounded-full font-medium text-sm transition shadow-sm border border-rose-200/60">
                        <span>🛍️ {{ __('Giỏ hàng') }}</span>
                        <span class="bg-rose-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-medium flex items-center justify-between shadow-sm">
                <span>✓ {{ session('success') }}</span>
                <a href="{{ route('checkout.index') }}" class="text-xs font-bold text-rose-600 hover:underline ml-4">Xem giỏ hàng & Thanh toán →</a>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm font-medium">
                ⚠ {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Slide-over Cart Drawer -->
    <div id="cart-drawer" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs" onclick="document.getElementById('cart-drawer').classList.add('hidden')"></div>
        <aside class="fixed top-0 right-0 h-full w-full max-w-md bg-[#FDFBF7] shadow-2xl border-l border-rose-100 flex flex-col justify-between z-10">
            <div class="p-6 border-b border-rose-100 flex items-center justify-between bg-white">
                <div>
                    <h2 class="font-serif text-xl font-bold text-stone-800">Giỏ Hàng Mỹ Phẩm</h2>
                    <p class="text-xs text-stone-500">{{ $cartCount }} sản phẩm đã chọn</p>
                </div>
                <button type="button" onclick="document.getElementById('cart-drawer').classList.add('hidden')" class="p-2 rounded-full hover:bg-rose-50 text-stone-500 font-bold">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($cart as $item)
                    <div class="flex gap-4 bg-white p-3.5 rounded-2xl border border-rose-100 shadow-xs">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-20 h-20 rounded-xl object-cover bg-rose-50 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold uppercase text-rose-500">{{ $item['brand_name'] ?? 'Lumina' }}</p>
                            <a href="{{ route('products.show', $item['slug']) }}" class="text-sm font-semibold text-stone-800 hover:text-rose-600 line-clamp-1">{{ $item['name'] }}</a>
                            <p class="text-xs text-stone-400">{{ $item['volume'] }} • {{ $item['skin_type'] }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm font-bold text-rose-600">{{ number_format($item['price'], 0, ',', '.') }}₫ x {{ $item['quantity'] }}</span>
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                    <button type="submit" class="text-xs text-stone-400 hover:text-red-500">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 space-y-3">
                        <p class="font-serif text-lg font-semibold text-stone-700">Giỏ hàng của bạn đang trống</p>
                        <p class="text-xs text-stone-400">Hãy khám phá các dòng serum, kem dưỡng và son môi chính hãng.</p>
                    </div>
                @endforelse
            </div>

            @if(count($cart) > 0)
                <div class="p-6 bg-white border-t border-rose-100 space-y-4">
                    <div class="flex items-center justify-between text-base font-bold">
                        <span>Tạm tính:</span>
                        <span class="text-rose-600 text-lg">{{ number_format($cartTotal, 0, ',', '.') }}₫</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3.5 rounded-full shadow-md shadow-rose-200 transition">
                        Tiến Hành Thanh Toán →
                    </a>
                </div>
            @endif
        </aside>
    </div>

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-300 mt-20 border-t border-rose-200/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="space-y-3">
                <span class="font-serif text-2xl font-bold text-white">
                    Lumina <span class="text-rose-400 italic font-normal">Beauty</span>
                </span>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Hệ thống phân phối mỹ phẩm cao cấp & chăm sóc da chuẩn khoa học chính hãng từ Pháp, Hàn Quốc và Nhật Bản. Vận hành trên nền tảng PHP Laravel 11 & Docker PostgreSQL.
                </p>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-rose-400 mb-3">Danh Mục Làm Đẹp</h4>
                <ul class="space-y-2 text-xs text-stone-400">
                    <li><a href="{{ route('products.index', ['category' => 'cham-soc-da']) }}" class="hover:text-white">Chăm sóc da chuyên sâu</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'trang-diem']) }}" class="hover:text-white">Trang điểm cao cấp</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'cham-soc-co-the']) }}" class="hover:text-white">Chăm sóc cơ thể</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'nuoc-hoa']) }}" class="hover:text-white">Nước hoa chính hãng</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-rose-400 mb-3">Theo Loại Da</h4>
                <ul class="space-y-2 text-xs text-stone-400">
                    <li><a href="{{ route('products.index', ['skin_type' => 'Da dầu']) }}" class="hover:text-white">Da dầu & Lỗ chân lông to</a></li>
                    <li><a href="{{ route('products.index', ['skin_type' => 'Da khô']) }}" class="hover:text-white">Da khô & Thiếu ẩm</a></li>
                    <li><a href="{{ route('products.index', ['skin_type' => 'Da nhạy cảm']) }}" class="hover:text-white">Da nhạy cảm & Phục hồi B5</a></li>
                    <li><a href="{{ route('products.index', ['skin_type' => 'Mọi loại da']) }}" class="hover:text-white">Mọi loại da</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-rose-400 mb-3">Liên Hệ & Hỗ Trợ</h4>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Hotline tư vấn da liễu: 1900 6868<br>
                    Showroom: 128 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh<br>
                    Cam kết 100% sản phẩm chính hãng có tem phụ tiếng Việt.
                </p>
            </div>
        </div>
        <div class="border-t border-stone-800 py-5 text-center text-xs text-stone-500">
            © 2026 Lumina Beauty — Powered by PHP Laravel 11, Eloquent ORM & Docker PostgreSQL.
        </div>
    </footer>

    @include('partials.i18n-en')
</body>
</html>
