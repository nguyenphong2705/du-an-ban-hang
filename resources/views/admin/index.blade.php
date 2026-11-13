<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị Viên Shop (Admin) | SHINE Beauty</title>
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
        const terracottaPalette = {
            50: '#FDF5F2',
            100: '#FBE8E1',
            200: '#F6D0C2',
            300: '#EEB09A',
            400: '#E28768',
            500: '#D46542',
            600: '#C04E2C',
            700: '#9E3E22',
            800: '#82341F',
            900: '#6B2D1D',
            950: '#3A150C',
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
                        terracotta: terracottaPalette,
                        wine: terracottaPalette
                    }
                }
            }
        }
    </script>
    <style>
        body, h1, h2, h3, h4, h5, h6, input, button, select, textarea {
            font-family: 'Roboto', sans-serif;
        }
        #desc-editor:empty:before {
            content: attr(data-placeholder);
            color: #a8a29e;
            pointer-events: none;
        }
        #desc-editor h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #292524;
            margin: 0.6rem 0 0.35rem;
        }
        #desc-editor h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #397A63;
            margin: 0.5rem 0 0.25rem;
        }
        #desc-editor ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin: 0.4rem 0;
        }
        #desc-editor ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
            margin: 0.4rem 0;
        }
        #desc-editor img {
            max-width: 100%;
            height: auto;
            border-radius: 0.875rem;
            margin: 0.65rem 0;
            border: 1px solid #DFF2EA;
        }
    </style>
</head>
<body class="font-sans antialiased bg-stone-100 text-stone-800 min-h-screen flex flex-col">
    <!-- Top Admin Header -->
    <header class="sticky top-0 z-40 bg-stone-900 text-white border-b border-stone-800 shadow-md">
        <div class="w-full px-5 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('logo-footer.png') }}?v=3" alt="SHINE Beauty Logo" width="48" height="48" class="w-12 h-12 object-contain drop-shadow-sm flex-shrink-0">
                    <div>
                        <span class="font-serif text-lg font-bold tracking-tight text-white">
                            Shine <span class="text-sage-400 font-normal italic">Admin</span>
                        </span>
                        <span class="hidden sm:inline-block ml-2 text-[10px] uppercase tracking-widest bg-sage-500/20 text-sage-300 border border-sage-500/30 px-2 py-0.5 rounded-full">
                            Quản Trị Viên Của Shop
                        </span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('customer.login') }}" target="_blank" class="text-xs font-medium bg-stone-800 hover:bg-stone-700 text-sage-300 px-3 py-2 rounded-xl border border-stone-700 transition">
                    👤 Trang Khách Hàng (/dang-nhap) ↗
                </a>
                <a href="{{ route('home') }}" target="_blank" class="text-xs font-medium bg-stone-800 hover:bg-stone-700 text-stone-200 px-3 py-2 rounded-xl border border-stone-700 transition">
                    🌐 Cửa Hàng ↗
                </a>

                <div class="hidden md:flex items-center gap-2 pl-2 border-l border-stone-700">
                    <div class="w-8 h-8 rounded-full bg-sage-500/20 border border-sage-400/40 text-sage-300 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="text-xs leading-tight">
                        <p class="font-semibold text-white">{{ auth()->user()->name ?? 'Quản trị viên' }}</p>
                        <p class="text-[11px] text-stone-400">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-semibold bg-red-500/15 hover:bg-red-500/25 text-red-300 border border-red-500/30 px-3 py-2 rounded-xl transition">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex-1 flex flex-col lg:flex-row w-full">
        <!-- Left Sidebar: Danh Mục Quản Trị & Danh Mục Mỹ Phẩm -->
        <aside class="w-full lg:w-72 flex-shrink-0 bg-stone-900 text-stone-200 border-b lg:border-b-0 lg:border-r border-stone-800 lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:overflow-y-auto p-5 space-y-6">
            <!-- 1. Danh Mục Quản Trị Hệ Thống -->
            <div class="space-y-2">
                <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-sage-400">
                    Danh Mục Quản Trị
                </p>
                <nav class="space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('admin.analytics') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition {{ $activePage === 'analytics' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <span class="flex items-center gap-2.5">
                            <span>📊</span>
                            <span>1. Thống Kê & Tồn Kho</span>
                        </span>
                        <span class="text-[10px] {{ $activePage === 'analytics' ? 'bg-white/20 text-white' : 'bg-stone-800 text-sage-300' }} px-2 py-0.5 rounded-full">{{ $stats['total_products'] }}</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition {{ $activePage === 'orders' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <span class="flex items-center gap-2.5">
                            <span>📦</span>
                            <span>2. Tra Cứu & Duyệt Đơn</span>
                        </span>
                        <span class="text-[10px] {{ $activePage === 'orders' ? 'bg-white/20 text-white' : 'bg-stone-800 text-sage-300' }} px-2 py-0.5 rounded-full">{{ $allOrders->count() }}</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition {{ $activePage === 'products' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <span class="flex items-center gap-2.5">
                            <span>💄</span>
                            <span>3. Quản Lý Sản Phẩm</span>
                        </span>
                        <span class="text-[10px] {{ $activePage === 'products' ? 'bg-white/20 text-white' : 'bg-stone-800 text-sage-300' }} px-2 py-0.5 rounded-full">{{ $allProducts->count() }}</span>
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition {{ $activePage === 'coupons' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <span class="flex items-center gap-2.5">
                            <span>🎟️</span>
                            <span>4. Quản Lý Voucher</span>
                        </span>
                        <span class="text-[10px] {{ $activePage === 'coupons' ? 'bg-white/20 text-white' : 'bg-stone-800 text-sage-300' }} px-2 py-0.5 rounded-full">{{ $coupons->count() }}</span>
                    </a>
                </nav>
            </div>

            <!-- 2. Danh Mục Mỹ Phẩm (Lọc & Thêm nhanh bên góc trái) -->
            <div class="pt-4 border-t border-stone-800 space-y-2.5">
                <div class="flex items-center justify-between px-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-sage-400">
                        Danh Mục Mỹ Phẩm ({{ $categories->count() }})
                    </p>
                    @if($categoryFilter !== 'ALL')
                        <a href="{{ route('admin.products.index') }}" class="text-[10px] text-sage-300 hover:underline">Bỏ lọc</a>
                    @endif
                </div>

                <div class="space-y-1 text-xs">
                    <a href="{{ route('admin.products.index', ['category_filter' => 'ALL', 'stock_filter' => $stockFilter]) }}"
                       class="flex items-center justify-between px-3.5 py-2 rounded-xl transition {{ $activePage === 'products' && $categoryFilter === 'ALL' ? 'bg-stone-800 text-sage-300 font-bold border border-sage-500/30' : 'text-stone-400 hover:bg-stone-800/60 hover:text-white' }}">
                        <span>✦ Tất cả danh mục</span>
                        <span class="text-[10px] opacity-80">{{ $allProducts->count() }} SP</span>
                    </a>
                    @foreach($categories as $c)
                        <a href="{{ route('admin.products.index', ['category_filter' => $c->id, 'stock_filter' => $stockFilter]) }}"
                           class="flex items-center justify-between px-3.5 py-2 rounded-xl transition {{ $activePage === 'products' && (string)$categoryFilter === (string)$c->id ? 'bg-stone-800 text-sage-300 font-bold border border-sage-500/30' : 'text-stone-400 hover:bg-stone-800/60 hover:text-white' }}">
                            <span class="truncate">{{ $c->name }}</span>
                            <span class="text-[10px] opacity-80">{{ $c->products_count ?? $allProducts->where('category_id', $c->id)->count() }} SP</span>
                        </a>
                    @endforeach
                </div>

                <!-- Form thêm nhanh Danh mục ở cột trái -->
                <form action="{{ route('admin.categories.store') }}" method="POST" class="pt-2 space-y-2">
                    @csrf
                    <div class="flex gap-1.5">
                        <input type="text" name="name" required placeholder="Thêm danh mục mới..."
                            class="w-full px-3 py-1.5 text-xs rounded-xl bg-stone-800 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-sage-400">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold flex-shrink-0 transition">
                            +
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Lọc Nhanh Theo Tình Trạng Kho -->
            <div class="pt-4 border-t border-stone-800 space-y-2">
                <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-sage-400">
                    Lọc Nhanh Kho Hàng
                </p>
                <div class="grid grid-cols-2 gap-1.5 text-[11px] font-semibold">
                    <a href="{{ route('admin.products.index', ['stock_filter' => 'BEST_SELLER']) }}" class="px-2.5 py-2 rounded-xl bg-stone-800/80 hover:bg-stone-800 text-amber-300 border border-stone-700/80 truncate">
                        ★ Bán chạy ({{ $stats['best_seller_count'] }})
                    </a>
                    <a href="{{ route('admin.products.index', ['stock_filter' => 'OUT_OF_STOCK']) }}" class="px-2.5 py-2 rounded-xl bg-stone-800/80 hover:bg-stone-800 text-red-400 border border-stone-700/80 truncate">
                        🔴 Hết hàng ({{ $stats['out_of_stock_count'] }})
                    </a>
                    <a href="{{ route('admin.products.index', ['stock_filter' => 'LOW_STOCK']) }}" class="px-2.5 py-2 rounded-xl bg-stone-800/80 hover:bg-stone-800 text-orange-300 border border-stone-700/80 truncate">
                        🟠 Sắp hết ({{ $stats['low_stock_count'] }})
                    </a>
                    <a href="{{ route('admin.products.index', ['stock_filter' => 'IN_STOCK']) }}" class="px-2.5 py-2 rounded-xl bg-stone-800/80 hover:bg-stone-800 text-emerald-300 border border-stone-700/80 truncate">
                        🟢 Còn hàng
                    </a>
                </div>
            </div>
        </aside>

        <!-- Right Main Content Area -->
        <main class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-medium flex items-center justify-between shadow-xs">
                <span>✓ {{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm font-medium">
                ⚠ {{ $errors->first() }}
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- 6 KPI SUMMARY CARDS: DOANH THU, ĐƠN HÀNG, BÁN CHẠY, HẾT HÀNG, SẮP HẾT, VOUCHER -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('admin.analytics') }}" class="bg-white p-4 rounded-3xl border border-stone-200/80 shadow-xs hover:border-sage-300 transition space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Tổng Doanh Thu</span>
                <p class="text-xl font-bold text-sage-600">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</p>
                <p class="text-[11px] text-emerald-600 font-medium">Thực thu: {{ number_format($stats['completed_revenue'], 0, ',', '.') }}₫</p>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="bg-white p-4 rounded-3xl border border-stone-200/80 shadow-xs hover:border-sage-300 transition space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Tổng Đơn Hàng</span>
                <p class="text-xl font-bold text-stone-800">{{ $stats['total_orders'] }} đơn</p>
                <p class="text-[11px] text-amber-600 font-medium">{{ $stats['pending_orders'] }} đơn đang xử lý</p>
            </a>

            <a href="{{ route('admin.products.index', ['stock_filter' => 'BEST_SELLER']) }}" class="bg-white p-4 rounded-3xl border border-amber-200 bg-amber-50/30 shadow-xs hover:border-amber-400 transition space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">★ SP Bán Chạy</span>
                <p class="text-xl font-bold text-amber-600">{{ $stats['best_seller_count'] }} SP</p>
                <p class="text-[11px] text-stone-500">Top doanh số cao nhất</p>
            </a>

            <a href="{{ route('admin.products.index', ['stock_filter' => 'OUT_OF_STOCK']) }}" class="bg-white p-4 rounded-3xl border border-red-200 bg-red-50/30 shadow-xs hover:border-red-400 transition space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-600">🔴 SP Hết Hàng (0)</span>
                <p class="text-xl font-bold text-red-600">{{ $stats['out_of_stock_count'] }} SP</p>
                <p class="text-[11px] text-red-500 font-medium">Cần nhập thêm ngay</p>
            </a>

            <a href="{{ route('admin.products.index', ['stock_filter' => 'LOW_STOCK']) }}" class="bg-white p-4 rounded-3xl border border-orange-200 bg-orange-50/30 shadow-xs hover:border-orange-400 transition space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600">🟠 Sắp Hết Hàng (≤15)</span>
                <p class="text-xl font-bold text-orange-600">{{ $stats['low_stock_count'] }} SP</p>
                <p class="text-[11px] text-stone-500">Tổng kho: {{ $stats['total_products'] }} SP</p>
            </a>

            <a href="{{ route('admin.coupons.index') }}" class="bg-white p-4 rounded-3xl border border-stone-200/80 shadow-xs hover:border-sage-300 transition space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Voucher & Khách</span>
                <p class="text-xl font-bold text-emerald-600">{{ $stats['active_coupons'] }} Voucher</p>
                <p class="text-[11px] text-stone-500">{{ $stats['customers_count'] }} TK khách hàng</p>
            </a>
        </div>

        @if($activePage === 'analytics')
        <!-- ========================================================================= -->
        <!-- SECTION 1: THỐNG KÊ RÕ RÀNG SẢN PHẨM BÁN CHẠY, HẾT HÀNG & SẮP HẾT HÀNG -->
        <!-- ========================================================================= -->
        <section id="section-analytics" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Top Selling / Best Seller Products -->
            <div class="lg:col-span-6 bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-widest text-amber-600">★ Thống Kê Doanh Số Sản Phẩm</span>
                        <h2 class="font-serif text-xl font-bold text-stone-800">
                            Sản Phẩm Bán Chạy Nhất ({{ $bestSellingProducts->count() }})
                        </h2>
                    </div>
                    <a href="{{ route('admin.products.index', ['stock_filter' => 'BEST_SELLER']) }}" class="text-xs font-semibold text-sage-600 hover:underline">
                        Lọc trong kho →
                    </a>
                </div>

                <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                    @foreach($bestSellingProducts as $idx => $prod)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-2xl border border-amber-100 bg-amber-50/20">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-7 h-7 rounded-xl bg-amber-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                    #{{ $idx + 1 }}
                                </span>
                                <img src="{{ $prod->first_image }}" alt="{{ $prod->name }}" class="w-12 h-12 rounded-xl object-cover bg-white border border-stone-200 flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">{{ $prod->product_code }}</span>
                                        <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="text-xs font-bold text-stone-800 hover:text-sage-600 line-clamp-1">
                                            {{ $prod->name }}
                                        </a>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-stone-500 mt-0.5">
                                        <span>Đã bán: <strong class="text-emerald-600">{{ $prod->sold_quantity }} SP</strong></span>
                                        <span>• Doanh thu: <strong class="text-sage-600">{{ number_format($prod->sold_revenue, 0, ',', '.') }}₫</strong></span>
                                        <span>• Tồn kho: <strong class="text-stone-800">{{ $prod->stock }} SP</strong></span>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.products.best-seller', $prod) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-[11px] font-bold px-2.5 py-1 rounded-xl border transition {{ $prod->is_best_seller ? 'bg-[#D4728C] text-white border-[#C25E79]' : 'bg-stone-100 text-stone-500 border-stone-200' }}">
                                    {{ $prod->is_best_seller ? '✦ Best Seller' : 'Đặt Best Seller' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Inventory Status, Standardization & Quick Stock Adjustment -->
            <div class="lg:col-span-6 bg-white rounded-3xl border border-stone-200/80 p-6 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-100 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-600">📦 Kiểm Soát Tồn Kho & Mã Sản Phẩm</span>
                        <h2 class="font-serif text-xl font-bold text-stone-800">
                            Hết Hàng ({{ $outOfStockProducts->count() }}) • Sắp Hết ({{ $lowStockProducts->count() }}) • Đủ Kho ({{ $allProducts->where('stock', '>', 15)->count() }})
                        </h2>
                    </div>
                    <form action="{{ route('admin.products.standardize') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl shadow-xs transition">
                            🔄 Chuẩn Hóa Toàn Bộ: Mã SP & Kho = 20 SP
                        </button>
                    </form>
                </div>

                @if($outOfStockProducts->isEmpty() && $lowStockProducts->isEmpty())
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs text-emerald-800 space-y-1">
                        <p class="font-bold text-sm">✓ Toàn bộ {{ $allProducts->count() }} sản phẩm đã được chuẩn hóa đồng bộ!</p>
                        <p>Tất cả sản phẩm đều có <strong>Mã sản phẩm (LMN-SP001 → LMN-SP{{ str_pad((string)$allProducts->count(), 3, '0', STR_PAD_LEFT) }})</strong> rõ ràng và số lượng tồn kho tiêu chuẩn là <strong>20 sản phẩm/mã</strong>.</p>
                    </div>
                @endif

                <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1">
                    @foreach(($outOfStockProducts->count() > 0 || $lowStockProducts->count() > 0) ? $outOfStockProducts->concat($lowStockProducts) : $allProducts as $prod)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-2xl border {{ $prod->stock <= 0 ? 'border-red-200 bg-red-50/40' : ($prod->stock <= 15 ? 'border-orange-200 bg-orange-50/40' : 'border-stone-200/80 bg-stone-50/60') }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $prod->first_image }}" alt="{{ $prod->name }}" class="w-11 h-11 rounded-xl object-cover bg-white border border-stone-200 flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">
                                            {{ $prod->product_code }}
                                        </span>
                                        @if($prod->stock <= 0)
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-red-600 text-white">HẾT HÀNG (0)</span>
                                        @elseif($prod->stock <= 15)
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-orange-500 text-white">SẮP HẾT ({{ $prod->stock }})</span>
                                        @else
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">TỒN KHO: {{ $prod->stock }} SP</span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-stone-800 line-clamp-1 mt-0.5">{{ $prod->name }}</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.products.stock', $prod) }}" method="POST" class="flex items-center gap-1.5 flex-shrink-0">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="stock" value="{{ $prod->stock }}" min="0" class="w-16 px-2 py-1 text-xs font-bold text-center rounded-xl border border-stone-300 bg-white">
                                <button type="submit" class="bg-stone-900 hover:bg-sage-600 text-white text-[11px] font-semibold px-2.5 py-1 rounded-xl transition">
                                    Lưu kho
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if($activePage === 'orders')
        <!-- ========================================================================= -->
        <!-- SECTION 2: TRA CỨU & QUẢN LÝ ĐƠN HÀNG -->
        <!-- ========================================================================= -->
        <section id="section-orders" class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-stone-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Tra Cứu & Duyệt Đơn Hàng</span>
                    <h2 class="font-serif text-2xl font-bold text-stone-800">
                        📦 Quản Lý Đơn Đặt Hàng ({{ $orders->count() }} / {{ $allOrders->count() }})
                    </h2>
                </div>

                <!-- Order Search & Status Filter Form -->
                <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="order_search" value="{{ $orderSearch }}" placeholder="Tìm mã đơn, tên khách, SĐT..."
                        class="px-3.5 py-2 text-xs rounded-xl border border-stone-300 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-300 w-56">
                    <select name="order_status" class="px-3 py-2 text-xs rounded-xl border border-stone-300 bg-stone-50">
                        <option value="ALL" {{ $orderStatus === 'ALL' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        <option value="PENDING" {{ $orderStatus === 'PENDING' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="CONFIRMED" {{ $orderStatus === 'CONFIRMED' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="SHIPPING" {{ $orderStatus === 'SHIPPING' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="COMPLETED" {{ $orderStatus === 'COMPLETED' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="CANCELLED" {{ $orderStatus === 'CANCELLED' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <button type="submit" class="bg-stone-900 hover:bg-sage-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                        🔍 Tra cứu đơn
                    </button>
                    @if($orderSearch !== '' || $orderStatus !== 'ALL')
                        <a href="{{ route('admin.orders.index') }}" class="text-xs text-stone-500 hover:text-stone-800 px-2 py-2">Xóa lọc</a>
                    @endif
                </form>
            </div>

            @if($orders->isEmpty())
                <div class="text-center py-12 text-sm text-stone-400">
                    Không tìm thấy đơn hàng nào khớp với điều kiện tra cứu.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 text-[11px] uppercase tracking-wider text-stone-400">
                                <th class="py-3 px-3">Mã Đơn</th>
                                <th class="py-3 px-3">Khách Hàng & Địa Chỉ</th>
                                <th class="py-3 px-3">Sản Phẩm Đặt Mua</th>
                                <th class="py-3 px-3">Thanh Toán & Voucher</th>
                                <th class="py-3 px-3">Trạng Thái Đơn</th>
                                <th class="py-3 px-3 text-right">Thao Tác Quản Trị</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($orders as $order)
                                <tr class="hover:bg-stone-50/80 align-top">
                                    <td class="py-4 px-3 font-mono font-bold text-stone-800 whitespace-nowrap">
                                        #ORDER-{{ $order->id }}
                                        <p class="text-[11px] font-sans font-normal text-stone-400 mt-0.5">
                                            {{ $order->created_at?->format('d/m/Y H:i') }}
                                        </p>
                                    </td>
                                    <td class="py-4 px-3 max-w-xs">
                                        <p class="font-bold text-stone-800">{{ $order->customer_name }}</p>
                                        <p class="text-xs font-semibold text-sage-600">{{ $order->phone }}</p>
                                        <p class="text-xs text-stone-500 mt-1">{{ $order->address }}</p>
                                        @if($order->note)
                                            <p class="text-[11px] italic text-amber-700 bg-amber-50 px-2 py-1 rounded-lg mt-1">
                                                Ghi chú: "{{ $order->note }}"
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-3">
                                        <ul class="space-y-1 text-xs">
                                            @foreach($order->items as $item)
                                                <li class="text-stone-700">
                                                    • <span class="font-mono font-bold text-[11px] text-stone-900">[{{ $item->product->product_code ?? ('LMN-SP' . str_pad((string)$item->product_id, 3, '0', STR_PAD_LEFT)) }}]</span>
                                                    <span class="font-medium">{{ $item->product->name ?? 'Sản phẩm #' . $item->product_id }}</span>
                                                    <span class="text-sage-600 font-bold">x{{ $item->quantity }}</span>
                                                    <span class="text-stone-400">({{ number_format($item->price, 0, ',', '.') }}₫)</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-4 px-3 whitespace-nowrap">
                                        @php
                                            $adminPmBadge = match($order->payment_method) {
                                                'BANK_TRANSFER' => ['label' => '🏦 QR Ngân hàng', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200'],
                                                'MOMO_QR' => ['label' => '📱 QR MoMo', 'class' => 'bg-pink-50 text-pink-700 border border-pink-200'],
                                                default => ['label' => '💵 COD', 'class' => 'bg-stone-100 text-stone-600'],
                                            };
                                        @endphp
                                        <p class="text-base font-bold text-sage-600">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
                                        <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $adminPmBadge['class'] }} mt-1">
                                            {{ $adminPmBadge['label'] }}
                                        </span>
                                        @if($order->coupon_code)
                                            <p class="text-[11px] text-emerald-600 font-semibold mt-1">
                                                🎟️ {{ $order->coupon_code }} (-{{ number_format($order->discount_amount, 0, ',', '.') }}₫)
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-3 whitespace-nowrap">
                                        @php
                                            $badgeColors = [
                                                'PENDING' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'CONFIRMED' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'SHIPPING' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'COMPLETED' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'CANCELLED' => 'bg-red-100 text-red-800 border-red-200',
                                            ];
                                            $statusLabels = [
                                                'PENDING' => 'Chờ xác nhận',
                                                'CONFIRMED' => 'Đã xác nhận',
                                                'SHIPPING' => 'Đang giao hàng',
                                                'COMPLETED' => 'Hoàn thành',
                                                'CANCELLED' => 'Đã hủy',
                                            ];
                                        @endphp
                                        <span class="inline-block text-xs font-bold px-3 py-1 rounded-full border {{ $badgeColors[$order->status] ?? 'bg-stone-100 text-stone-700' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="text-xs rounded-xl border border-stone-300 px-2.5 py-1.5 bg-white">
                                                    @foreach(['PENDING' => 'Chờ xác nhận', 'CONFIRMED' => 'Đã xác nhận', 'SHIPPING' => 'Đang giao', 'COMPLETED' => 'Hoàn thành', 'CANCELLED' => 'Hủy đơn'] as $stKey => $stVal)
                                                        <option value="{{ $stKey }}" {{ $order->status === $stKey ? 'selected' : '' }}>{{ $stVal }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-stone-900 hover:bg-sage-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                                                    Lưu
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Xóa đơn hàng #ORDER-{{ $order->id }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:bg-red-50 px-2.5 py-1.5 rounded-xl border border-red-200 transition">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
        @endif

        @if($activePage === 'products')
        <!-- ========================================================================= -->
        <!-- SECTION 3: XEM, THÊM, SỬA, XÓA SẢN PHẨM MỸ PHẨM ĐẦY ĐỦ -->
        <!-- ========================================================================= -->
        <section id="section-products" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Add / Edit Product Form -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs h-fit space-y-5">
                <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-sage-500">
                            {{ $editingProduct ? 'Chỉnh Sửa Sản Phẩm' : 'Thêm Sản Phẩm Mới' }}
                        </span>
                        <h3 class="font-serif text-xl font-bold text-stone-800">
                            {{ $editingProduct ? $editingProduct->name : 'Nhập Mỹ Phẩm Vào Kho' }}
                        </h3>
                    </div>
                    @if($editingProduct)
                        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-800 bg-stone-100 px-3 py-1.5 rounded-xl">
                            ✕ Hủy sửa
                        </a>
                    @endif
                </div>

                <form action="{{ $editingProduct ? route('admin.products.update', $editingProduct) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @if($editingProduct)
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Mã SP (SKU) *</label>
                            <input type="text" name="sku" value="{{ old('sku', $editingProduct->product_code ?? ('LMN-SP' . str_pad((string)($allProducts->count() + 1), 3, '0', STR_PAD_LEFT))) }}" placeholder="LMN-SP013" class="w-full px-3 py-2.5 text-sm font-mono font-bold uppercase rounded-2xl border border-stone-200 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-300">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Tên sản phẩm mỹ phẩm *</label>
                            <input type="text" name="name" required value="{{ old('name', $editingProduct->name ?? '') }}" placeholder="VD: Tinh Chất Phục Hồi B5 La Roche-Posay" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Giá gốc (VNĐ) *</label>
                            <input type="number" name="price" required value="{{ old('price', $editingProduct->price ?? '') }}" placeholder="650000" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Giá khuyến mãi (VNĐ)</label>
                            <input type="number" name="sale_price" value="{{ old('sale_price', $editingProduct->sale_price ?? '') }}" placeholder="520000" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Danh mục *</label>
                            <select name="category_id" required class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('category_id', $editingProduct->category_id ?? '') == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Thương hiệu *</label>
                            <select name="brand_id" required class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ (old('brand_id', $editingProduct->brand_id ?? '') == $brand->id) ? 'selected' : '' }}>
                                        {{ $brand->name }} ({{ $brand->origin }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Loại da *</label>
                            <select name="skin_type" class="w-full px-3 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                                @foreach(['Mọi loại da', 'Da dầu', 'Da khô', 'Da nhạy cảm'] as $st)
                                    <option value="{{ $st }}" {{ (old('skin_type', $editingProduct->skin_type ?? '') === $st) ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Dung tích *</label>
                            <input type="text" name="volume" required value="{{ old('volume', $editingProduct->volume ?? '30ml') }}" placeholder="30ml / 50g" class="w-full px-3 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Tồn kho chuẩn *</label>
                            <input type="number" name="stock" required min="0" value="{{ old('stock', $editingProduct->stock ?? 20) }}" class="w-full px-3 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1.5">Hình ảnh sản phẩm (Chọn từ máy) *</label>
                        <div class="p-3.5 rounded-2xl border-2 border-dashed border-sage-200 bg-sage-50/40 space-y-3">
                            <div class="flex items-center gap-3.5">
                                <div id="product-image-preview-wrap" class="{{ $editingProduct ? 'block' : 'hidden' }} relative flex-shrink-0">
                                    <img id="product-image-preview"
                                         src="{{ $editingProduct ? $editingProduct->first_image : '' }}"
                                         alt="Preview"
                                         class="w-16 h-16 rounded-2xl object-cover bg-white border border-sage-200 shadow-xs">
                                </div>
                                <div class="flex-1 min-w-0 space-y-1.5">
                                    <label for="product-image-input" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-semibold cursor-pointer shadow-xs transition">
                                        <span>📁 Chọn ảnh từ máy tính</span>
                                    </label>
                                    <input type="file" id="product-image-input" name="image_file" accept="image/*" class="hidden" onchange="previewSelectedProductImage(this)">
                                    <p id="product-image-filename" class="text-[11px] text-stone-500 truncate">
                                        {{ $editingProduct ? 'Đang dùng ảnh hiện tại (Chọn tệp mới để thay thế)' : 'Hỗ trợ JPG, PNG, WEBP, GIF (Tối đa 5MB)' }}
                                    </p>
                                </div>
                                <button type="button" id="clear-selected-image-btn" onclick="clearSelectedProductImage()" class="hidden text-[11px] font-semibold text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-xl border border-red-200 transition cursor-pointer">
                                    ✕ Bỏ chọn
                                </button>
                            </div>

                            @if($editingProduct)
                                <label class="flex items-center gap-2 pt-2 border-t border-sage-200/60 text-xs text-red-600 font-medium cursor-pointer">
                                    <input type="checkbox" id="remove-existing-image" name="remove_image" value="1" class="rounded accent-red-500" onchange="toggleRemoveExistingImage(this)">
                                    <span>🗑️ Xóa ảnh hiện tại của sản phẩm này</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Bảng thành phần (Ingredients)</label>
                        <textarea name="ingredients" rows="2" placeholder="Aqua, Niacinamide 10%, Zinc PCA 1%..." class="w-full px-3.5 py-2 text-sm rounded-2xl border border-stone-200 bg-stone-50">{{ old('ingredients', $editingProduct->ingredients ?? '') }}</textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold uppercase text-stone-600">Mô tả công dụng chi tiết (Rich Text & Hình ảnh)</label>
                            <span id="desc-upload-status" class="text-[11px] font-semibold text-sage-600 hidden">⏳ Đang tải ảnh lên...</span>
                        </div>

                        <div class="rounded-2xl border border-stone-200 bg-white overflow-hidden focus-within:ring-2 focus-within:ring-sage-300">
                            <!-- Rich Text Toolbar -->
                            <div class="flex flex-wrap items-center gap-1 p-2 bg-stone-100 border-b border-stone-200 text-xs">
                                <button type="button" onclick="execDescCmd('bold')" title="In đậm (Ctrl+B)" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 font-bold text-stone-700 cursor-pointer">
                                    B
                                </button>
                                <button type="button" onclick="execDescCmd('italic')" title="In nghiêng (Ctrl+I)" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 italic font-serif text-stone-700 cursor-pointer">
                                    I
                                </button>
                                <button type="button" onclick="execDescCmd('underline')" title="Gạch chân (Ctrl+U)" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 underline text-stone-700 cursor-pointer">
                                    U
                                </button>

                                <span class="h-4 w-px bg-stone-300 mx-0.5"></span>

                                <button type="button" onclick="execDescCmd('formatBlock', 'H2')" title="Tiêu đề lớn" class="px-2 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 font-bold text-stone-700 cursor-pointer">
                                    H2
                                </button>
                                <button type="button" onclick="execDescCmd('formatBlock', 'H3')" title="Tiêu đề phụ" class="px-2 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 font-bold text-sage-700 cursor-pointer">
                                    H3
                                </button>
                                <button type="button" onclick="execDescCmd('formatBlock', 'P')" title="Đoạn văn thường" class="px-2 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 text-stone-600 cursor-pointer">
                                    P
                                </button>

                                <span class="h-4 w-px bg-stone-300 mx-0.5"></span>

                                <button type="button" onclick="execDescCmd('insertUnorderedList')" title="Danh sách dấu chấm" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 text-stone-700 cursor-pointer">
                                    • Danh sách
                                </button>
                                <button type="button" onclick="execDescCmd('insertOrderedList')" title="Danh sách đánh số" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 text-stone-700 cursor-pointer">
                                    1. Danh sách
                                </button>

                                <span class="h-4 w-px bg-stone-300 mx-0.5"></span>

                                <button type="button" onclick="execDescCmd('foreColor', '#397A63')" title="Màu chữ Sage Green" class="px-2 py-1.5 rounded-lg bg-white hover:bg-sage-50 border border-stone-200 font-bold text-sage-600 cursor-pointer">
                                    A
                                </button>
                                <button type="button" onclick="execDescCmd('removeFormat')" title="Xóa định dạng" class="px-2 py-1.5 rounded-lg bg-white hover:bg-stone-200 border border-stone-200 text-stone-500 cursor-pointer">
                                    Tx
                                </button>

                                <span class="h-4 w-px bg-stone-300 mx-0.5"></span>

                                <!-- Insert Image from Computer Button -->
                                <label for="desc-editor-image-input" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sage-500 hover:bg-sage-600 text-white font-semibold cursor-pointer shadow-2xs transition">
                                    <span>🖼️ Chèn ảnh từ máy</span>
                                </label>
                                <input type="file" id="desc-editor-image-input" accept="image/*" multiple class="hidden" onchange="uploadDescEditorImages(this.files)">
                            </div>

                            <!-- Editable Rich Content Area -->
                            <div id="desc-editor"
                                 contenteditable="true"
                                 data-placeholder="Nhập mô tả chi tiết công dụng, hướng dẫn sử dụng và bấm '🖼️ Chèn ảnh từ máy' (hoặc kéo-thả / dán ảnh trực tiếp vào đây)..."
                                 oninput="syncDescEditor()"
                                 class="w-full min-h-[180px] max-h-[380px] overflow-y-auto px-4 py-3 text-sm text-stone-700 leading-relaxed focus:outline-none bg-stone-50/40 focus:bg-white">{!! old('description', $editingProduct->description ?? '') !!}</div>
                        </div>

                        <textarea name="description" id="description-hidden-input" class="hidden">{{ old('description', $editingProduct->description ?? '') }}</textarea>
                        <p class="text-[11px] text-stone-400 mt-1">
                            💡 Có thể bôi đậm, tạo danh sách và chèn nhiều ảnh minh họa trực tiếp vào nội dung mô tả.
                        </p>
                    </div>

                    <label class="flex items-center gap-2 text-xs font-semibold text-stone-700 cursor-pointer">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $editingProduct->is_best_seller ?? false) ? 'checked' : '' }} class="rounded accent-sage-500">
                        <span>Đánh dấu là Sản phẩm Bán chạy (Best Seller)</span>
                    </label>

                    <button type="submit" class="w-full bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3.5 rounded-2xl shadow-md shadow-sage-200 transition text-sm">
                        {{ $editingProduct ? '💾 Lưu Cập Nhật Sản Phẩm' : '+ Thêm Mỹ Phẩm Vào Kho' }}
                    </button>
                </form>
            </div>

            <!-- Right: Product List with Search & Stock Filter -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-stone-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Quản Lý Kho Hàng</span>
                        <h3 class="font-serif text-xl font-bold text-stone-800">
                            Danh Sách Sản Phẩm ({{ $products->count() }} / {{ $allProducts->count() }})
                        </h3>
                    </div>

                    <!-- Filter Tabs for Stock Status -->
                    <div class="flex flex-wrap gap-1.5 text-xs">
                        @foreach([
                            'ALL' => 'Tất cả (' . $allProducts->count() . ')',
                            'BEST_SELLER' => '★ Bán chạy',
                            'OUT_OF_STOCK' => '🔴 Hết hàng (' . $stats['out_of_stock_count'] . ')',
                            'LOW_STOCK' => '🟠 Sắp hết (' . $stats['low_stock_count'] . ')',
                            'IN_STOCK' => '🟢 Còn nhiều',
                        ] as $sfKey => $sfLabel)
                            <a href="{{ route('admin.products.index', ['stock_filter' => $sfKey, 'product_search' => $productSearch, 'category_filter' => $categoryFilter]) }}"
                                class="px-2.5 py-1 rounded-xl font-semibold transition {{ $stockFilter === $sfKey ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                                {{ $sfLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Product Search Bar -->
                <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap gap-2">
                    <input type="hidden" name="stock_filter" value="{{ $stockFilter }}">
                    <input type="text" name="product_search" value="{{ $productSearch }}" placeholder="Tìm tên sản phẩm, thương hiệu, loại da..."
                        class="flex-1 px-3.5 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50">
                    <select name="category_filter" class="px-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50">
                        <option value="ALL">Mọi danh mục</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ (string)$categoryFilter === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-stone-900 hover:bg-sage-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                        Lọc SP
                    </button>
                </form>

                <div class="space-y-3 max-h-[650px] overflow-y-auto pr-1">
                    @forelse($products as $product)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-3.5 rounded-2xl border {{ $product->stock <= 0 ? 'border-red-200 bg-red-50/30' : ($product->stock <= 15 ? 'border-orange-200 bg-orange-50/30' : 'border-stone-100 bg-stone-50/50') }} hover:border-sage-200 transition">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-14 h-14 rounded-2xl object-cover bg-white border border-stone-200 flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">
                                            {{ $product->product_code }}
                                        </span>
                                        <span class="text-[10px] font-bold uppercase text-sage-500">{{ $product->brand->name ?? '' }}</span>
                                        <span class="text-[10px] text-stone-400">• {{ $product->category->name ?? '' }}</span>
                                        @if($product->is_best_seller)
                                            <span class="text-[10px] bg-[#D4728C] text-white border border-[#C25E79] font-bold px-2 py-0.5 rounded-full">✦ Best Seller</span>
                                        @endif
                                        @if($product->stock <= 0)
                                            <span class="text-[10px] bg-red-600 text-white font-bold px-2 py-0.5 rounded-full">HẾT HÀNG</span>
                                        @elseif($product->stock <= 15)
                                            <span class="text-[10px] bg-orange-500 text-white font-bold px-2 py-0.5 rounded-full">SẮP HẾT ({{ $product->stock }} SP)</span>
                                        @else
                                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Tồn kho: {{ $product->stock }} SP</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-sm font-bold text-stone-800 hover:text-sage-600 line-clamp-1">
                                        {{ $product->name }}
                                    </a>
                                    <div class="flex items-center gap-3 text-xs mt-0.5">
                                        <span class="font-bold text-sage-600">{{ number_format($product->final_price, 0, ',', '.') }}₫</span>
                                        @if($product->sale_price)
                                            <span class="line-through text-stone-400">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                        @endif
                                        <span class="text-stone-500">Đã bán: <strong>{{ $product->sold_quantity ?? 0 }}</strong></span>
                                        <span class="text-stone-400">{{ $product->volume }} • {{ $product->skin_type }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 flex-shrink-0">
                                <a href="{{ route('admin.products.index', ['edit_product' => $product->id]) }}" class="text-xs font-semibold bg-white hover:bg-sage-50 text-stone-700 border border-stone-200 px-3 py-1.5 rounded-xl transition">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Xóa sản phẩm {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-xl transition">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-10 text-xs text-stone-400">Không có sản phẩm nào khớp với bộ lọc.</p>
                    @endforelse
                </div>
            </div>
        </section>
        @endif

        @if($activePage === 'coupons')
        <!-- ========================================================================= -->
        <!-- SECTION 4: QUẢN LÝ MÃ GIẢM GIÁ (VOUCHER) & DANH MỤC -->
        <!-- ========================================================================= -->
        <section id="section-coupons" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs h-fit space-y-4">
                <div class="border-b border-stone-100 pb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Khuyến Mãi Cho Khách Hàng</span>
                    <h3 class="font-serif text-xl font-bold text-stone-800">Phát Hành Voucher Mới</h3>
                </div>

                <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Mã Voucher *</label>
                            <input type="text" name="code" required placeholder="VD: VIP25" class="w-full px-3.5 py-2.5 text-sm font-mono uppercase rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Giảm (%) *</label>
                            <input type="number" name="discount_percent" required min="1" max="90" value="20" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Mô tả ưu đãi (Hiển thị ở Ví Voucher Khách hàng)</label>
                        <input type="text" name="description" placeholder="VD: Giảm 20% toàn bộ sản phẩm chăm sóc da" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Đơn tối thiểu (VNĐ)</label>
                            <input type="number" name="min_order_amount" value="200000" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Hạn sử dụng *</label>
                            <input type="date" name="expires_at" required value="{{ now()->addMonths(3)->format('Y-m-d') }}" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-sage-600 text-white font-semibold py-3 rounded-2xl transition text-sm">
                        🎟️ Tạo Mã Giảm Giá Mới
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs space-y-4">
                <div class="border-b border-stone-100 pb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Danh Sách Mã Giảm Giá</span>
                    <h3 class="font-serif text-xl font-bold text-stone-800">Các Voucher Đang Phát Hành ({{ $coupons->count() }})</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($coupons as $coupon)
                        <div class="p-4 rounded-2xl border {{ $coupon->is_active ? 'border-terracotta-200 bg-gradient-to-r from-terracotta-50 via-white to-terracotta-50/60' : 'border-stone-200 bg-stone-100 opacity-70' }} flex items-center justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-base text-terracotta-900">{{ $coupon->code }}</span>
                                    <span class="text-xs font-bold bg-terracotta-600 text-white px-2 py-0.5 rounded-full">
                                        -{{ $coupon->discount_percent }}%
                                    </span>
                                </div>
                                <p class="text-[11px] text-stone-600 mt-1 line-clamp-1">{{ $coupon->description }}</p>
                                <p class="text-[11px] text-stone-400 mt-0.5">
                                    HSD: {{ $coupon->expires_at?->format('d/m/Y') }}
                                    • <strong class="{{ $coupon->is_active ? 'text-terracotta-700' : 'text-stone-500' }}">{{ $coupon->is_active ? 'Đang bật' : 'Đã tắt' }}</strong>
                                </p>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <form action="{{ route('admin.coupons.toggle', $coupon) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold px-2.5 py-1.5 rounded-xl border border-stone-300 bg-white hover:bg-stone-50">
                                        {{ $coupon->is_active ? 'Tắt' : 'Bật' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
        </main>
    </div>

    <script>
        const initialProductImage = @json($editingProduct ? $editingProduct->first_image : '');

        function previewSelectedProductImage(input) {
            const previewWrap = document.getElementById('product-image-preview-wrap');
            const previewImg = document.getElementById('product-image-preview');
            const filenameEl = document.getElementById('product-image-filename');
            const clearBtn = document.getElementById('clear-selected-image-btn');
            const removeCheckbox = document.getElementById('remove-existing-image');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewWrap.classList.remove('hidden');
                    previewWrap.classList.add('block');
                };
                reader.readAsDataURL(file);
                filenameEl.textContent = 'Đã chọn: ' + file.name;
                clearBtn.classList.remove('hidden');
                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                }
            }
        }

        function clearSelectedProductImage() {
            const input = document.getElementById('product-image-input');
            const previewWrap = document.getElementById('product-image-preview-wrap');
            const previewImg = document.getElementById('product-image-preview');
            const filenameEl = document.getElementById('product-image-filename');
            const clearBtn = document.getElementById('clear-selected-image-btn');

            input.value = '';
            clearBtn.classList.add('hidden');

            if (initialProductImage) {
                previewImg.src = initialProductImage;
                previewWrap.classList.remove('hidden');
                previewWrap.classList.add('block');
                filenameEl.textContent = 'Đang dùng ảnh hiện tại (Chọn tệp mới để thay thế)';
            } else {
                previewImg.src = '';
                previewWrap.classList.add('hidden');
                previewWrap.classList.remove('block');
                filenameEl.textContent = 'Hỗ trợ JPG, PNG, WEBP, GIF (Tối đa 5MB)';
            }
        }

        function toggleRemoveExistingImage(checkbox) {
            const previewWrap = document.getElementById('product-image-preview-wrap');
            const filenameEl = document.getElementById('product-image-filename');
            const input = document.getElementById('product-image-input');
            const clearBtn = document.getElementById('clear-selected-image-btn');

            if (checkbox.checked) {
                input.value = '';
                clearBtn.classList.add('hidden');
                previewWrap.classList.add('hidden');
                previewWrap.classList.remove('block');
                filenameEl.textContent = 'Ảnh hiện tại sẽ được xóa khi bấm Lưu cập nhật';
            } else if (initialProductImage) {
                previewWrap.classList.remove('hidden');
                previewWrap.classList.add('block');
                filenameEl.textContent = 'Đang dùng ảnh hiện tại (Chọn tệp mới để thay thế)';
            }
        }

        // Rich Text Editor Functions for Product Description
        function syncDescEditor() {
            const editor = document.getElementById('desc-editor');
            const hiddenInput = document.getElementById('description-hidden-input');
            if (!editor || !hiddenInput) return;
            hiddenInput.value = editor.innerHTML.trim();
        }

        function execDescCmd(command, value = null) {
            const editor = document.getElementById('desc-editor');
            if (!editor) return;
            editor.focus();
            document.execCommand(command, false, value);
            syncDescEditor();
        }

        async function uploadDescEditorImages(fileList) {
            const editor = document.getElementById('desc-editor');
            const statusEl = document.getElementById('desc-upload-status');
            const fileInput = document.getElementById('desc-editor-image-input');
            if (!editor || !fileList || fileList.length === 0) return;

            if (statusEl) statusEl.classList.remove('hidden');

            for (const file of Array.from(fileList)) {
                if (!file.type.startsWith('image/')) continue;

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', @json(csrf_token()));

                try {
                    const res = await fetch(@json(route('admin.products.upload-editor-image')), {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();
                    if (res.ok && data.url) {
                        editor.focus();
                        const imgHtml = `<p><img src="${data.url}" alt="Ảnh mô tả sản phẩm" class="rounded-2xl my-2 max-w-full h-auto shadow-xs border border-sage-100"></p>`;
                        document.execCommand('insertHTML', false, imgHtml);
                        syncDescEditor();
                    }
                } catch (err) {
                    console.error('Upload editor image failed:', err);
                }
            }

            if (statusEl) statusEl.classList.add('hidden');
            if (fileInput) fileInput.value = '';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const editor = document.getElementById('desc-editor');
            if (!editor) return;

            const form = editor.closest('form');
            if (form) {
                form.addEventListener('submit', function () {
                    syncDescEditor();
                });
            }

            // Support pasting images directly from clipboard
            editor.addEventListener('paste', function (e) {
                const items = e.clipboardData && e.clipboardData.items;
                if (!items) return;
                const imageFiles = [];
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        const f = items[i].getAsFile();
                        if (f) imageFiles.push(f);
                    }
                }
                if (imageFiles.length > 0) {
                    e.preventDefault();
                    uploadDescEditorImages(imageFiles);
                }
            });

            // Support drag & drop images directly into editor
            editor.addEventListener('drop', function (e) {
                const files = e.dataTransfer && e.dataTransfer.files;
                if (files && files.length > 0) {
                    e.preventDefault();
                    uploadDescEditorImages(files);
                }
            });
        });

        // Smooth Hash Navigation (#section-coupons, #section-products, etc.)
        function scrollToAdminHash() {
            const hash = window.location.hash;
            if (!hash || !hash.startsWith('#')) return;
            const targetEl = document.getElementById(hash.slice(1));
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        window.addEventListener('DOMContentLoaded', scrollToAdminHash);
        window.addEventListener('hashchange', scrollToAdminHash);
    </script>
</body>
</html>
