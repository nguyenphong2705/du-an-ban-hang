<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Shine Beauty | Mỹ Phẩm & Chăm Sóc Da Cao Cấp Chính Hãng'))</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <script>
        const sageMintPalette = {
            50: '#F2F9F6',
            100: '#DFF2EA',
            200: '#BFE4D5',
            300: '#94CEB8',
            400: '#68B398',
            500: '#4A947A',
            600: '#397A63',
            700: '#2F6250',
            800: '#274F42',
            900: '#214237',
            950: '#11251E',
        };
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                        serif: ['Roboto', 'sans-serif'],
                    },
                    colors: {
                        sage: sageMintPalette,
                        rose: sageMintPalette,
                        mint: {
                            50: '#F0FDF7',
                            100: '#DCFCEB',
                            200: '#BBF7D8',
                            300: '#86EFBA',
                            400: '#4ADE96',
                            500: '#2AB876',
                            600: '#1D965E',
                            700: '#19774C',
                            800: '#185E3F',
                            900: '#154E35',
                        },
                        wine: {
                            50: '#FDF2F4',
                            100: '#FCE4E8',
                            200: '#F8C9D2',
                            300: '#F19EAE',
                            400: '#E56982',
                            500: '#B8334C',
                            600: '#8F243A',
                            700: '#722F37',
                            800: '#5E1924',
                            900: '#4A121B',
                            950: '#2D090F',
                        }
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
<body class="font-sans antialiased min-h-screen flex flex-col bg-[#F5FAF7] text-stone-800">
    @php
        $cart = session('cart', []);
        $cartCount = collect($cart)->sum('quantity');
        $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
    @endphp

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-sage-200/80 via-sage-100 to-sage-200/80 text-sage-900 text-xs py-2 px-4 text-center font-medium tracking-wide">
        {{ __('✨ Shine Beauty — Miễn phí vận chuyển toàn quốc cho đơn từ 500.000₫ — Cam kết 100% Mỹ phẩm Chính hãng ✨') }}
    </div>

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 bg-[#F5FAF7]/95 backdrop-blur-md border-b border-sage-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('logo.png') }}?v=2" alt="Shine Beauty Logo" class="w-14 h-14 sm:w-16 sm:h-16 object-contain drop-shadow-xs group-hover:scale-105 transition">
                    <div>
                        <span class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-stone-800">
                            Shine <span class="text-sage-500 font-normal italic">Beauty</span>
                        </span>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-stone-400 -mt-1">
                            Luxury Cosmetics
                        </p>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-5 text-sm font-medium">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-sage-600 font-semibold' : 'text-stone-700 hover:text-sage-500' }}">{{ __('Trang Chủ') }}</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') && !request('category') ? 'text-sage-600 font-semibold' : 'text-stone-700 hover:text-sage-500' }}">{{ __('Sản Phẩm') }}</a>
                    <a href="{{ route('products.index', ['category' => 'cham-soc-da']) }}" class="{{ request('category') === 'cham-soc-da' ? 'text-sage-600 font-semibold' : 'text-stone-700 hover:text-sage-500' }}">{{ __('Chăm Sóc Da') }}</a>
                    <a href="{{ route('orders.track') }}" class="{{ request()->routeIs('orders.track') ? 'text-sage-600 font-semibold' : 'text-stone-700 hover:text-sage-500' }}">{{ __('Tra Cứu Đơn Hàng') }}</a>

                    @if(auth()->check() && !auth()->user()->is_admin)
                        <a href="{{ route('customer.account') }}" class="text-xs font-bold bg-sage-500 hover:bg-sage-600 text-white px-3.5 py-2 rounded-full transition shadow-xs">
                            👤 {{ auth()->user()->username ?? auth()->user()->name }} ({{ __('Đơn & Voucher') }})
                        </a>
                    @else
                        <button type="button" onclick="openAuthModal('login')" class="text-xs font-bold bg-sage-100 hover:bg-sage-200 text-sage-800 px-3.5 py-2 rounded-full transition border border-sage-200 cursor-pointer">
                            👤 {{ __('Đăng Nhập / Đăng Ký') }}
                        </button>
                    @endif
                </nav>

                <!-- Language Switcher & Cart Button -->
                <div class="flex items-center gap-2.5">
                    @if(auth()->check() && !auth()->user()->is_admin)
                        <a href="{{ route('customer.account') }}" class="md:hidden text-xs font-bold bg-sage-500 hover:bg-sage-600 text-white px-3 py-2 rounded-full transition shadow-xs">
                            👤
                        </a>
                    @else
                        <button type="button" onclick="openAuthModal('login')" class="md:hidden text-xs font-bold bg-sage-100 hover:bg-sage-200 text-sage-800 px-3 py-2 rounded-full transition border border-sage-200 cursor-pointer" title="{{ __('Đăng Nhập / Đăng Ký') }}">
                            👤
                        </button>
                    @endif
                    <div class="inline-flex items-center rounded-full bg-white border border-sage-200 p-0.5 shadow-2xs text-xs font-bold">
                        <a href="{{ route('lang.switch', 'vi') }}"
                           title="Tiếng Việt (Mặc định)"
                           class="px-2.5 py-1.5 rounded-full transition flex items-center gap-1.5 {{ app()->getLocale() === 'vi' ? 'bg-[#DA251D] text-white shadow-2xs' : 'text-stone-600 hover:text-[#DA251D]' }}">
                            <svg class="w-4 h-3 rounded-[2px] shadow-2xs flex-shrink-0 {{ app()->getLocale() === 'vi' ? 'ring-1 ring-white/60' : '' }}" viewBox="0 0 30 20" xmlns="http://www.w3.org/2000/svg">
                                <rect width="30" height="20" fill="#DA251D"/>
                                <polygon points="15,4 16.47,8.52 21.22,8.52 17.38,11.31 18.85,15.83 15,13.04 11.15,15.83 12.62,11.31 8.78,8.52 13.53,8.52" fill="#FFFF00"/>
                            </svg>
                            <span>VI</span>
                        </a>
                        <a href="{{ route('lang.switch', 'en') }}"
                           title="Switch to English (Global)"
                           class="px-2.5 py-1.5 rounded-full transition flex items-center gap-1.5 {{ app()->getLocale() === 'en' ? 'bg-[#0284C7] text-white shadow-2xs' : 'text-stone-600 hover:text-[#0284C7]' }}">
                            <svg class="w-4 h-3 rounded-[2px] shadow-2xs flex-shrink-0 {{ app()->getLocale() === 'en' ? 'ring-1 ring-white/60' : '' }}" viewBox="0 0 30 20" xmlns="http://www.w3.org/2000/svg">
                                <rect width="30" height="20" fill="#0284C7"/>
                                <circle cx="15" cy="10" r="6.5" fill="none" stroke="#FFFFFF" stroke-width="1.3"/>
                                <ellipse cx="15" cy="10" rx="3.2" ry="6.5" fill="none" stroke="#FFFFFF" stroke-width="1.2"/>
                                <line x1="8.5" y1="10" x2="21.5" y2="10" stroke="#FFFFFF" stroke-width="1.2"/>
                                <path d="M9.5,6.8 Q15,8 20.5,6.8 M9.5,13.2 Q15,12 20.5,13.2" fill="none" stroke="#FFFFFF" stroke-width="1"/>
                            </svg>
                            <span>EN</span>
                        </a>
                    </div>

                    <button type="button" onclick="document.getElementById('cart-drawer').classList.remove('hidden')" class="relative flex items-center gap-2 bg-sage-50 hover:bg-sage-100 text-sage-700 px-4 py-2.5 rounded-full font-medium text-sm transition shadow-sm border border-sage-200/60">
                        <span>🛍️ {{ __('Giỏ Hàng') }}</span>
                        <span class="bg-sage-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
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
                <a href="{{ route('checkout.index') }}" class="text-xs font-bold text-sage-600 hover:underline ml-4">Xem giỏ hàng & Thanh toán →</a>
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
        <aside class="fixed top-0 right-0 h-full w-full max-w-md bg-[#F5FAF7] shadow-2xl border-l border-sage-100 flex flex-col justify-between z-10">
            <div class="p-6 border-b border-sage-100 flex items-center justify-between bg-white">
                <div>
                    <h2 class="font-serif text-xl font-bold text-stone-800">Giỏ Hàng Mỹ Phẩm</h2>
                    <p class="text-xs text-stone-500">{{ $cartCount }} sản phẩm đã chọn</p>
                </div>
                <button type="button" onclick="document.getElementById('cart-drawer').classList.add('hidden')" class="p-2 rounded-full hover:bg-sage-50 text-stone-500 font-bold">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($cart as $item)
                    <div class="flex gap-4 bg-white p-3.5 rounded-2xl border border-sage-100 shadow-xs">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-20 h-20 rounded-xl object-cover bg-sage-50 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold uppercase text-sage-500">{{ $item['brand_name'] ?? 'Shine' }}</p>
                            <a href="{{ route('products.show', $item['slug']) }}" class="text-sm font-semibold text-stone-800 hover:text-sage-600 line-clamp-1">{{ $item['name'] }}</a>
                            <p class="text-xs text-stone-400">{{ $item['volume'] }} • {{ $item['skin_type'] }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm font-bold text-sage-600">{{ number_format($item['price'], 0, ',', '.') }}₫ x {{ $item['quantity'] }}</span>
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
                @php
                    $drawerShippingFee = $cartTotal < 500000 ? 30000 : 0;
                    $drawerFreeShipDiff = max(0, 500000 - $cartTotal);
                    $drawerProgressPercent = min(100, (int) round(($cartTotal / 500000) * 100));
                @endphp
                <div class="p-6 bg-white border-t border-sage-100 space-y-3.5">
                    <div class="bg-[#F5FAF7] border border-sage-100 rounded-2xl p-3 space-y-1.5 text-xs">
                        @if($drawerFreeShipDiff > 0)
                            <p class="text-stone-600">
                                🚚 Mua thêm <strong class="text-sage-600">{{ number_format($drawerFreeShipDiff, 0, ',', '.') }}₫</strong> để được <strong>Miễn phí vận chuyển toàn quốc</strong> (đơn từ 500.000₫)
                            </p>
                        @else
                            <p class="text-emerald-700 font-semibold">
                                ✨ Đơn hàng đủ điều kiện <strong>Miễn phí vận chuyển toàn quốc</strong>!
                            </p>
                        @endif
                        <div class="w-full h-1.5 bg-sage-100 rounded-full overflow-hidden">
                            <div class="h-full bg-sage-500 rounded-full transition-all duration-300" style="width: {{ $drawerProgressPercent }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-1.5 text-xs text-stone-500">
                        <div class="flex items-center justify-between">
                            <span>Tạm tính:</span>
                            <span class="font-semibold text-stone-700">{{ number_format($cartTotal, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phí vận chuyển toàn quốc:</span>
                            @if($drawerShippingFee > 0)
                                <span class="font-semibold text-amber-600">+{{ number_format($drawerShippingFee, 0, ',', '.') }}₫</span>
                            @else
                                <span class="font-semibold text-emerald-600">Miễn phí (0₫)</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-base font-bold pt-2 border-t border-sage-100">
                        <span>Tổng dự kiến:</span>
                        <span class="text-sage-600 text-lg">{{ number_format($cartTotal + $drawerShippingFee, 0, ',', '.') }}₫</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3.5 rounded-full shadow-md shadow-sage-200 transition">
                        Tiến Hành Thanh Toán →
                    </a>
                </div>
            @endif
        </aside>
    </div>

    <!-- Auth Popup / Modal (Login & Sign Up) -->
    @if(!auth()->check() || auth()->user()->is_admin)
        @php
            $initialAuthTab = old('auth_tab', session('open_auth_modal', request('auth', 'login')));
            if (!in_array($initialAuthTab, ['login', 'register'], true)) {
                $initialAuthTab = 'login';
            }
            $hasAuthErrors = $errors->hasAny(['login', 'password', 'name', 'username', 'email', 'phone', 'address']);
            $shouldAutoOpenAuthModal = $hasAuthErrors || session()->has('open_auth_modal') || in_array(request('auth'), ['login', 'register'], true);
        @endphp
        <div id="auth-modal" class="fixed inset-0 z-50 {{ $shouldAutoOpenAuthModal ? 'flex' : 'hidden' }} items-center justify-center p-4 sm:p-6 overflow-y-auto">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs transition-opacity" onclick="closeAuthModal()"></div>

            <!-- Modal Box -->
            <div class="relative w-full max-w-lg bg-white rounded-3xl border border-sage-100 shadow-2xl overflow-hidden z-10 my-auto">
                <!-- Modal Header Banner -->
                <div class="bg-gradient-to-r from-sage-500 to-sage-600 px-6 py-5 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/95 p-1.5 shadow-sm flex items-center justify-center flex-shrink-0">
                            <img src="{{ asset('logo.png') }}?v=3" alt="Shine Beauty Logo" width="40" height="40" class="w-10 h-10 object-contain">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-sage-100 block">Thành Viên Shine Beauty</span>
                            <h2 id="auth-modal-title" class="font-serif text-xl font-bold">
                                {{ $initialAuthTab === 'register' ? 'Đăng Ký Tài Khoản Mới' : 'Đăng Nhập Khách Hàng' }}
                            </h2>
                        </div>
                    </div>
                    <button type="button" onclick="closeAuthModal()" class="w-8 h-8 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center font-bold transition cursor-pointer" aria-label="Đóng">
                        ✕
                    </button>
                </div>

                <!-- Tab Switcher (Login / Sign Up) -->
                <div class="px-6 pt-5">
                    <div class="grid grid-cols-2 p-1 bg-sage-50/80 rounded-2xl border border-sage-100 text-xs font-bold">
                        <button type="button" id="auth-tab-btn-login" onclick="switchAuthTab('login')"
                            class="py-2.5 rounded-xl transition cursor-pointer {{ $initialAuthTab === 'login' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:text-sage-600' }}">
                            🔑 Đăng Nhập
                        </button>
                        <button type="button" id="auth-tab-btn-register" onclick="switchAuthTab('register')"
                            class="py-2.5 rounded-xl transition cursor-pointer {{ $initialAuthTab === 'register' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:text-sage-600' }}">
                            ✨ Đăng Ký Mới
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[82vh] overflow-y-auto">
                    @if($hasAuthErrors)
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-medium">
                            ⚠ {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Equal-size Grid Stack for Login & Register -->
                    <div class="grid grid-cols-1">
                        <!-- TAB 1: LOGIN FORM -->
                        <div id="auth-panel-login" class="col-start-1 row-start-1 flex flex-col justify-between h-full transition-opacity duration-150 {{ $initialAuthTab === 'login' ? 'opacity-100 visible pointer-events-auto z-10' : 'opacity-0 invisible pointer-events-none z-0' }}">
                            <div class="space-y-4">
                                <p class="text-xs text-stone-500">
                                    Đăng nhập bằng <strong>Tên đăng nhập</strong> hoặc <strong>Email</strong> để xem đơn hàng, giỏ hàng đã lưu và kho voucher ưu đãi.
                                </p>

                                <form id="auth-form-login" action="{{ route('customer.login.submit') }}" method="POST" class="space-y-3.5 text-xs">
                                    @csrf
                                    <input type="hidden" name="auth_tab" value="login">

                                    <div>
                                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">
                                            Tên đăng nhập hoặc Email *
                                        </label>
                                        <input type="text" id="modal-cust-login" name="login" required value="{{ old('login') }}"
                                            placeholder="Nhập tên đăng nhập hoặc email của bạn"
                                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">
                                            Mật khẩu *
                                        </label>
                                        <input type="password" id="modal-cust-password" name="password" required
                                            placeholder="••••••••••••"
                                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-stone-500 pt-0.5">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="remember" checked class="rounded accent-sage-500">
                                            <span>Tự động lưu giỏ hàng & phiên đăng nhập</span>
                                        </label>
                                        <a href="{{ route('customer.password.request') }}" class="text-sage-500 hover:text-sage-600 hover:underline font-semibold">
                                            Quên Mật Khẩu?
                                        </a>
                                    </div>

                                    <!-- Member Perks Box to balance visual density with Register form -->
                                    <div class="rounded-2xl bg-sage-50/70 border border-sage-100 p-3.5 space-y-1.5 text-xs text-stone-600">
                                        <p class="font-bold text-sage-600 text-[11px] uppercase tracking-wider">✦ Quyền lợi thành viên Shine</p>
                                        <p class="text-[11px] leading-relaxed">- Giỏ hàng thông minh: Tự động lưu trữ và khôi phục giỏ hàng khi đăng nhập lại.</p>
                                        <p class="text-[11px] leading-relaxed">- Ưu đãi độc quyền: Nhanh chóng áp dụng các mã giảm giá dành riêng cho thành viên như LUMINA20, FLASHSALE30.</p>
                                    </div>
                                </form>
                            </div>

                            <div class="space-y-3.5 pt-3">
                                <button type="submit" form="auth-form-login" class="w-full bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3.5 rounded-2xl shadow-md shadow-sage-200 transition text-sm cursor-pointer">
                                    Đăng Nhập Vào Tài Khoản Khách Hàng →
                                </button>

                                <div class="pt-3 border-t border-sage-100 flex items-center justify-between text-xs text-stone-500">
                                    <span>Chưa có tài khoản thành viên?</span>
                                    <button type="button" onclick="switchAuthTab('register')" class="font-bold text-sage-600 hover:underline cursor-pointer">
                                        Đăng ký tài khoản mới →
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: REGISTER (SIGN UP) FORM -->
                        <div id="auth-panel-register" class="col-start-1 row-start-1 flex flex-col justify-between h-full transition-opacity duration-150 {{ $initialAuthTab === 'register' ? 'opacity-100 visible pointer-events-auto z-10' : 'opacity-0 invisible pointer-events-none z-0' }}">
                            <div class="space-y-4">
                                <p class="text-xs text-stone-500">
                                    Tạo tài khoản miễn phí để lưu giỏ hàng tự động, theo dõi vận chuyển và nhận ngay mã giảm giá thành viên mới.
                                </p>

                                <form id="auth-form-register" action="{{ route('customer.register') }}" method="POST" class="space-y-3.5 text-xs">
                                    @csrf
                                    <input type="hidden" name="auth_tab" value="register">

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">Họ và tên *</label>
                                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="Họ và tên *"
                                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">Tên đăng nhập *</label>
                                            <input type="text" name="username" required value="{{ old('username') }}" placeholder="Tên đăng nhập *"
                                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">Email *</label>
                                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="Email *"
                                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">Số điện thoại *</label>
                                            <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="Số điện thoại *"
                                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">Địa chỉ nhận hàng</label>
                                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Địa chỉ nhận hàng mặc định"
                                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-stone-600 mb-1">Mật khẩu *</label>
                                        <input type="password" name="password" required placeholder="Mật khẩu (tối thiểu 6 ký tự) *"
                                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                                    </div>
                                </form>
                            </div>

                            <div class="space-y-3.5 pt-3">
                                <button type="submit" form="auth-form-register" class="w-full bg-stone-900 hover:bg-sage-600 text-white font-semibold py-3.5 rounded-2xl shadow-md transition text-sm cursor-pointer">
                                    Tạo Tài Khoản Khách Hàng Mới →
                                </button>

                                <div class="pt-3 border-t border-sage-100 flex items-center justify-between text-xs text-stone-500">
                                    <span>Đã có tài khoản thành viên?</span>
                                    <button type="button" onclick="switchAuthTab('login')" class="font-bold text-sage-600 hover:underline cursor-pointer">
                                        ← Quay lại Đăng nhập
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openAuthModal(tab = 'login') {
                const modal = document.getElementById('auth-modal');
                if (!modal) return;
                switchAuthTab(tab);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeAuthModal() {
                const modal = document.getElementById('auth-modal');
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            function switchAuthTab(tab) {
                const loginPanel = document.getElementById('auth-panel-login');
                const registerPanel = document.getElementById('auth-panel-register');
                const loginBtn = document.getElementById('auth-tab-btn-login');
                const registerBtn = document.getElementById('auth-tab-btn-register');
                const modalTitle = document.getElementById('auth-modal-title');
                const isEn = document.documentElement.lang === 'en';

                if (!loginPanel || !registerPanel) return;

                const activePanelClasses = 'col-start-1 row-start-1 flex flex-col justify-between h-full transition-opacity duration-150 opacity-100 visible pointer-events-auto z-10';
                const inactivePanelClasses = 'col-start-1 row-start-1 flex flex-col justify-between h-full transition-opacity duration-150 opacity-0 invisible pointer-events-none z-0';

                if (tab === 'register') {
                    loginPanel.className = inactivePanelClasses;
                    registerPanel.className = activePanelClasses;
                    registerBtn.className = 'py-2.5 rounded-xl transition cursor-pointer bg-sage-500 text-white shadow-xs';
                    loginBtn.className = 'py-2.5 rounded-xl transition cursor-pointer text-stone-600 hover:text-sage-600';
                    if (modalTitle) {
                        modalTitle.textContent = isEn ? 'Create New Account' : 'Đăng Ký Tài Khoản Mới';
                    }
                } else {
                    registerPanel.className = inactivePanelClasses;
                    loginPanel.className = activePanelClasses;
                    loginBtn.className = 'py-2.5 rounded-xl transition cursor-pointer bg-sage-500 text-white shadow-xs';
                    registerBtn.className = 'py-2.5 rounded-xl transition cursor-pointer text-stone-600 hover:text-sage-600';
                    if (modalTitle) {
                        modalTitle.textContent = isEn ? 'Customer Login' : 'Đăng Nhập Khách Hàng';
                    }
                }
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeAuthModal();
                }
            });
        </script>
    @endif

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-300 mt-20 border-t border-sage-200/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo-footer.png') }}?v=1" alt="Shine Beauty Logo" class="w-14 h-14 object-contain drop-shadow-sm">
                    <span class="font-serif text-2xl font-bold text-white">
                        Shine <span class="text-sage-400 italic font-normal">Beauty</span>
                    </span>
                </div>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Hệ thống phân phối mỹ phẩm cao cấp & chăm sóc da chuẩn khoa học chính hãng từ Pháp, Hàn Quốc và Nhật Bản.
                </p>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-sage-400 mb-3">Danh Mục Làm Đẹp</h4>
                <ul class="space-y-2 text-xs text-stone-400">
                    <li><a href="{{ route('products.index', ['category' => 'cham-soc-da']) }}" class="hover:text-white">Chăm sóc da chuyên sâu</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'trang-diem']) }}" class="hover:text-white">Trang điểm cao cấp</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'cham-soc-co-the']) }}" class="hover:text-white">Chăm sóc cơ thể</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'nuoc-hoa']) }}" class="hover:text-white">Nước hoa chính hãng</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-sage-400 mb-3">Theo Loại Da</h4>
                <ul class="space-y-2 text-xs text-stone-400">
                    <li><a href="{{ route('products.index', ['skin_type' => 'Da dầu']) }}" class="hover:text-white">Da dầu & Lỗ chân lông to</a></li>
                    <li><a href="{{ route('products.index', ['skin_type' => 'Da khô']) }}" class="hover:text-white">Da khô & Thiếu ẩm</a></li>
                    <li><a href="{{ route('products.index', ['skin_type' => 'Da nhạy cảm']) }}" class="hover:text-white">Da nhạy cảm & Phục hồi B5</a></li>
                    <li><a href="{{ route('products.index', ['skin_type' => 'Mọi loại da']) }}" class="hover:text-white">Mọi loại da</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-sage-400 mb-3">Liên Hệ & Hỗ Trợ</h4>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Hotline tư vấn da liễu: 1900 6868<br>
                    Cửa Hàng: 28 Phạm Văn Chiêu, Phường Thông Tây Hội, Tp. Hồ Chí Minh<br>
                    Chúng tôi cam kết cung cấp sản phẩm chính hãng 100%, đi kèm tem phụ tiếng Việt theo đúng quy định.
                </p>
            </div>
        </div>
    </footer>

    @include('partials.i18n-en')
</body>
</html>
