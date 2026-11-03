<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh Chủ Shop (Back-Office) | Lumina Beauty Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-stone-100 text-stone-800 min-h-screen flex flex-col">
    <!-- Top Admin Bar -->
    <header class="sticky top-0 z-40 bg-stone-900 text-white border-b border-stone-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.index') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-500 to-rose-300 flex items-center justify-center text-white font-bold shadow">
                        ✦
                    </div>
                    <div>
                        <span class="font-serif text-lg font-bold tracking-tight text-white">
                            Lumina <span class="text-rose-400 font-normal italic">Shop Owner</span>
                        </span>
                        <span class="hidden sm:inline-block ml-2 text-[10px] uppercase tracking-widest bg-rose-500/20 text-rose-300 border border-rose-500/30 px-2 py-0.5 rounded-full">
                            Hệ Thống Quản Trị Chủ Shop
                        </span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="text-xs font-medium bg-stone-800 hover:bg-stone-700 text-stone-200 px-3.5 py-2 rounded-xl border border-stone-700 transition">
                    🌐 Xem Cửa Hàng Khách Mua ↗
                </a>

                <div class="hidden md:flex items-center gap-2.5 pl-3 border-l border-stone-700">
                    <div class="w-8 h-8 rounded-full bg-rose-500/20 border border-rose-400/40 text-rose-300 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="text-xs leading-tight">
                        <p class="font-semibold text-white">{{ auth()->user()->name ?? 'Chủ Shop' }}</p>
                        <p class="text-[11px] text-stone-400">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-semibold bg-red-500/15 hover:bg-red-500/25 text-red-300 border border-red-500/30 px-3.5 py-2 rounded-xl transition">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 flex-1">
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

        <!-- KPI Summary Cards for Shop Owner -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-stone-200/80 shadow-xs space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Tổng Doanh Thu Đơn</span>
                <p class="text-2xl font-bold text-rose-600">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</p>
                <p class="text-[11px] text-emerald-600 font-medium">Đã hoàn tất: {{ number_format($stats['completed_revenue'], 0, ',', '.') }}₫</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-stone-200/80 shadow-xs space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Đơn Hàng Cần Xử Lý</span>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_orders'] }} đơn</p>
                <p class="text-[11px] text-stone-500">Tổng tất cả: {{ $stats['total_orders'] }} đơn hàng</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-stone-200/80 shadow-xs space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Sản Phẩm Trong Kho</span>
                <p class="text-2xl font-bold text-stone-800">{{ $stats['total_products'] }} SP</p>
                <p class="text-[11px] text-rose-500 font-medium">{{ $stats['low_stock_products'] }} SP sắp hết hàng (≤35)</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-stone-200/80 shadow-xs space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Voucher Khuyến Mãi</span>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['active_coupons'] }} mã</p>
                <p class="text-[11px] text-stone-500">Đang kích hoạt tại Checkout</p>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-stone-200/80 shadow-xs space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Danh Mục & Hãng</span>
                <p class="text-2xl font-bold text-stone-800">{{ $categories->count() }} / {{ $brands->count() }}</p>
                <p class="text-[11px] text-stone-500">{{ $categories->count() }} danh mục • {{ $brands->count() }} thương hiệu</p>
            </div>
        </div>

        <!-- Quick Navigation Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-stone-200 pb-4">
            <a href="#section-orders" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-stone-900 text-white shadow-xs">
                📦 1. Duyệt Đơn Hàng ({{ $orders->count() }})
            </a>
            <a href="#section-products" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-white hover:bg-rose-50 text-stone-700 border border-stone-200">
                💄 2. Quản Lý Kho Mỹ Phẩm ({{ $products->count() }})
            </a>
            <a href="#section-coupons" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-white hover:bg-rose-50 text-stone-700 border border-stone-200">
                🎟️ 3. Mã Giảm Giá / Voucher ({{ $coupons->count() }})
            </a>
            <a href="#section-catalog" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-white hover:bg-rose-50 text-stone-700 border border-stone-200">
                🏷️ 4. Danh Mục & Thương Hiệu
            </a>
        </div>

        <!-- SECTION 1: ORDERS MANAGEMENT -->
        <section id="section-orders" class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Quản Lý Đơn Đặt Hàng</span>
                    <h2 class="font-serif text-2xl font-bold text-stone-800">Danh Sách Đơn Hàng Khách Mua ({{ $orders->count() }})</h2>
                </div>
                <span class="text-xs text-stone-500">Chủ shop duyệt trạng thái đơn hàng trực tiếp bên dưới</span>
            </div>

            @if($orders->isEmpty())
                <div class="text-center py-12 text-sm text-stone-400">
                    Hiện chưa có đơn đặt hàng nào trong hệ thống.
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
                                <th class="py-3 px-3 text-right">Thao Tác Chủ Shop</th>
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
                                        <p class="text-xs font-semibold text-rose-600">{{ $order->phone }}</p>
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
                                                    • <span class="font-medium">{{ $item->product->name ?? 'Sản phẩm #' . $item->product_id }}</span>
                                                    <span class="text-rose-600 font-bold">x{{ $item->quantity }}</span>
                                                    <span class="text-stone-400">({{ number_format($item->price, 0, ',', '.') }}₫)</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-4 px-3 whitespace-nowrap">
                                        <p class="text-base font-bold text-rose-600">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
                                        <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-stone-100 text-stone-600 mt-1">
                                            {{ $order->payment_method }}
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
                                                <select name="status" class="text-xs rounded-xl border border-stone-300 px-2.5 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-rose-300">
                                                    @foreach(['PENDING' => 'Chờ xác nhận', 'CONFIRMED' => 'Đã xác nhận', 'SHIPPING' => 'Đang giao', 'COMPLETED' => 'Hoàn thành', 'CANCELLED' => 'Hủy đơn'] as $stKey => $stVal)
                                                        <option value="{{ $stKey }}" {{ $order->status === $stKey ? 'selected' : '' }}>{{ $stVal }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-stone-900 hover:bg-rose-600 text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition">
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

        <!-- SECTION 2: PRODUCTS MANAGEMENT (ADD / EDIT / DELETE) -->
        <section id="section-products" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Form Thêm / Sửa Mỹ Phẩm -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs h-fit space-y-5">
                <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-rose-500">
                            {{ $editingProduct ? 'Chỉnh Sửa Mỹ Phẩm' : 'Thêm Mỹ Phẩm Mới' }}
                        </span>
                        <h3 class="font-serif text-xl font-bold text-stone-800">
                            {{ $editingProduct ? $editingProduct->name : 'Nhập Sản Phẩm Vào Kho' }}
                        </h3>
                    </div>
                    @if($editingProduct)
                        <a href="{{ route('admin.index') }}#section-products" class="text-xs font-semibold text-stone-500 hover:text-stone-800 bg-stone-100 px-3 py-1.5 rounded-xl">
                            ✕ Hủy sửa
                        </a>
                    @endif
                </div>

                <form action="{{ $editingProduct ? route('admin.products.update', $editingProduct) : route('admin.products.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @if($editingProduct)
                        @method('PUT')
                    @endif

                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Tên sản phẩm mỹ phẩm *</label>
                        <input type="text" name="name" required value="{{ old('name', $editingProduct->name ?? '') }}" placeholder="VD: Tinh Chất Phục Hồi B5 La Roche-Posay" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-300">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Giá gốc (VNĐ) *</label>
                            <input type="number" name="price" required value="{{ old('price', $editingProduct->price ?? '') }}" placeholder="650000" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Giá Flash Sale (VNĐ)</label>
                            <input type="number" name="sale_price" value="{{ old('sale_price', $editingProduct->sale_price ?? '') }}" placeholder="520000" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-300">
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
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Tồn kho *</label>
                            <input type="number" name="stock" required value="{{ old('stock', $editingProduct->stock ?? 80) }}" class="w-full px-3 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">URL Hình ảnh sản phẩm</label>
                        <input type="url" name="image" value="{{ old('image', $editingProduct->first_image ?? '') }}" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Bảng thành phần (Ingredients)</label>
                        <textarea name="ingredients" rows="2" placeholder="Aqua, Niacinamide 10%, Zinc PCA 1%, Hyaluronic Acid..." class="w-full px-3.5 py-2 text-sm rounded-2xl border border-stone-200 bg-stone-50">{{ old('ingredients', $editingProduct->ingredients ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Mô tả công dụng</label>
                        <textarea name="description" rows="2" placeholder="Công dụng nổi bật của sản phẩm..." class="w-full px-3.5 py-2 text-sm rounded-2xl border border-stone-200 bg-stone-50">{{ old('description', $editingProduct->description ?? '') }}</textarea>
                    </div>

                    <label class="flex items-center gap-2 text-xs font-semibold text-stone-700 cursor-pointer">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $editingProduct->is_best_seller ?? false) ? 'checked' : '' }} class="rounded accent-rose-500">
                        <span>Đánh dấu là Sản phẩm Bán chạy (Best Seller) trên Trang chủ</span>
                    </label>

                    <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3.5 rounded-2xl shadow-md shadow-rose-200 transition text-sm">
                        {{ $editingProduct ? '💾 Lưu Cập Nhật Sản Phẩm' : '+ Thêm Mỹ Phẩm Vào Kho' }}
                    </button>
                </form>
            </div>

            <!-- Danh sách sản phẩm trong kho -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs space-y-5">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Danh Mục Hàng Hóa</span>
                        <h3 class="font-serif text-xl font-bold text-stone-800">Kho Mỹ Phẩm Hiện Tại ({{ $products->count() }})</h3>
                    </div>
                </div>

                <div class="space-y-3 max-h-[680px] overflow-y-auto pr-1">
                    @foreach($products as $product)
                        <div class="flex items-center justify-between gap-4 p-3.5 rounded-2xl border border-stone-100 hover:border-rose-200 bg-stone-50/50 transition">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-14 h-14 rounded-2xl object-cover bg-white border border-stone-200 flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold uppercase text-rose-500">{{ $product->brand->name ?? '' }}</span>
                                        <span class="text-[10px] text-stone-400">• {{ $product->category->name ?? '' }}</span>
                                        @if($product->is_best_seller)
                                            <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">★ Best Seller</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-sm font-bold text-stone-800 hover:text-rose-600 line-clamp-1">
                                        {{ $product->name }}
                                    </a>
                                    <div class="flex items-center gap-3 text-xs mt-0.5">
                                        <span class="font-bold text-rose-600">{{ number_format($product->final_price, 0, ',', '.') }}₫</span>
                                        @if($product->sale_price)
                                            <span class="line-through text-stone-400">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                        @endif
                                        <span class="text-stone-500">Kho: <strong>{{ $product->stock }}</strong></span>
                                        <span class="text-stone-400">{{ $product->volume }} • {{ $product->skin_type }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('admin.index', ['edit_product' => $product->id]) }}#section-products" class="text-xs font-semibold bg-white hover:bg-rose-50 text-stone-700 border border-stone-200 px-3 py-1.5 rounded-xl transition">
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
                    @endforeach
                </div>
            </div>
        </section>

        <!-- SECTION 3: COUPONS / VOUCHER MANAGEMENT -->
        <section id="section-coupons" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs h-fit space-y-4">
                <div class="border-b border-stone-100 pb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Khuyến Mãi & Flash Sale</span>
                    <h3 class="font-serif text-xl font-bold text-stone-800">Phát Hành Mã Giảm Giá (Voucher)</h3>
                </div>

                <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Mã Code Voucher *</label>
                        <input type="text" name="code" required placeholder="VD: SUMMER25, VIP50" class="w-full px-3.5 py-2.5 text-sm font-mono uppercase rounded-2xl border border-stone-200 bg-stone-50">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Phần trăm giảm (%) *</label>
                            <input type="number" name="discount_percent" required min="1" max="90" value="20" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-stone-600 mb-1">Hạn sử dụng *</label>
                            <input type="date" name="expires_at" required value="{{ now()->addMonths(3)->format('Y-m-d') }}" class="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-stone-200 bg-stone-50">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-stone-900 hover:bg-rose-600 text-white font-semibold py-3 rounded-2xl transition text-sm">
                        🎟️ Tạo Mã Giảm Giá Mới
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7 bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs space-y-4">
                <div class="border-b border-stone-100 pb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Danh Sách Voucher</span>
                    <h3 class="font-serif text-xl font-bold text-stone-800">Các Mã Giảm Giá Đang Có ({{ $coupons->count() }})</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($coupons as $coupon)
                        <div class="p-4 rounded-2xl border {{ $coupon->is_active ? 'border-rose-200 bg-rose-50/40' : 'border-stone-200 bg-stone-100 opacity-70' }} flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-base text-stone-900">{{ $coupon->code }}</span>
                                    <span class="text-xs font-bold bg-rose-500 text-white px-2 py-0.5 rounded-full">
                                        -{{ $coupon->discount_percent }}%
                                    </span>
                                </div>
                                <p class="text-xs text-stone-500 mt-1">
                                    HSD: {{ $coupon->expires_at?->format('d/m/Y') }}
                                    • <strong class="{{ $coupon->is_active ? 'text-emerald-600' : 'text-stone-500' }}">{{ $coupon->is_active ? 'Đang bật' : 'Đã tắt' }}</strong>
                                </p>
                            </div>

                            <div class="flex items-center gap-1.5">
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

        <!-- SECTION 4: CATEGORIES & BRANDS -->
        <section id="section-catalog" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs space-y-4">
                <h3 class="font-serif text-xl font-bold text-stone-800">Danh Mục Mỹ Phẩm ({{ $categories->count() }})</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $c)
                        <span class="text-xs font-semibold bg-stone-100 text-stone-700 px-3 py-1.5 rounded-full">
                            {{ $c->name }} ({{ $c->products_count }} SP)
                        </span>
                    @endforeach
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-2 pt-2">
                    @csrf
                    <input type="text" name="name" required placeholder="Tên danh mục mới..." class="flex-1 px-3.5 py-2 text-sm rounded-xl border border-stone-200 bg-stone-50">
                    <button type="submit" class="bg-stone-900 hover:bg-rose-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                        + Thêm Danh Mục
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-3xl border border-stone-200/80 p-6 sm:p-7 shadow-xs space-y-4">
                <h3 class="font-serif text-xl font-bold text-stone-800">Thương Hiệu Phân Phối ({{ $brands->count() }})</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($brands as $b)
                        <span class="text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-full">
                            {{ $b->name }} - {{ $b->origin }} ({{ $b->products_count }} SP)
                        </span>
                    @endforeach
                </div>
                <form action="{{ route('admin.brands.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2">
                    @csrf
                    <input type="text" name="name" required placeholder="Tên thương hiệu..." class="px-3.5 py-2 text-sm rounded-xl border border-stone-200 bg-stone-50">
                    <input type="text" name="origin" required placeholder="Xuất xứ (Pháp, Hàn...)" class="px-3.5 py-2 text-sm rounded-xl border border-stone-200 bg-stone-50">
                    <button type="submit" class="bg-stone-900 hover:bg-rose-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                        + Thêm Thương Hiệu
                    </button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
