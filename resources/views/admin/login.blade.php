<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Kênh Chủ Shop | Lumina Beauty Back-Office</title>
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
<body class="font-sans antialiased min-h-screen bg-stone-950 text-stone-100 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-x-hidden">
    <!-- Subtle Luxury Background Glow -->
    <div class="fixed -top-40 -left-40 w-96 h-96 rounded-full bg-rose-500/15 blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full mx-auto space-y-6 relative z-10">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-rose-500 to-rose-300 flex items-center justify-center text-white shadow-lg shadow-rose-500/30">
                    <span class="text-xl">✦</span>
                </div>
                <div class="text-left">
                    <span class="font-serif text-2xl font-bold tracking-tight text-white">
                        Lumina <span class="text-rose-400 font-normal italic">Admin</span>
                    </span>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-stone-400 -mt-1">
                        Shop Owner Back-Office
                    </p>
                </div>
            </a>
            <h1 class="font-serif text-2xl font-bold text-white pt-3">
                Cổng Quản Trị Dành Cho Chủ Shop
            </h1>
            <p class="text-xs text-stone-400">
                Quản lý doanh thu, duyệt đơn hàng, kho mỹ phẩm & phát hành mã giảm giá
            </p>
        </div>

        <!-- Flash / Error Alerts -->
        @if(session('error'))
            <div class="bg-red-500/15 border border-red-500/40 text-red-200 px-4 py-3 rounded-2xl text-xs font-medium">
                ⚠ {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-500/15 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl text-xs font-medium">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/15 border border-red-500/40 text-red-200 px-4 py-3 rounded-2xl text-xs font-medium">
                ⚠ {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Card -->
        <div class="bg-stone-900/90 backdrop-blur-xl border border-stone-800 rounded-3xl p-7 shadow-2xl space-y-6">
            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-300 mb-2">
                        Email Chủ Shop (Admin)
                    </label>
                    <input type="email" id="email-input" name="email" value="{{ old('email', 'admin@luminabeauty.vn') }}" required autofocus
                        placeholder="admin@luminabeauty.vn"
                        class="w-full px-4 py-3 text-sm rounded-2xl bg-stone-950 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-300 mb-2">
                        Mật khẩu Quản trị
                    </label>
                    <input type="password" id="password-input" name="password" value="Admin@123456" required
                        placeholder="••••••••••••"
                        class="w-full px-4 py-3 text-sm rounded-2xl bg-stone-950 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <div class="flex items-center justify-between text-xs text-stone-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded accent-rose-500">
                        <span>Ghi nhớ phiên Chủ Shop</span>
                    </label>
                    <span class="text-rose-400 font-medium">Bảo mật Laravel Auth</span>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold py-3.5 rounded-2xl shadow-lg shadow-rose-500/25 transition text-sm">
                    Đăng Nhập Vào Kênh Chủ Shop →
                </button>
            </form>

            <!-- Quick Credentials Box for Shop Owner -->
            <div class="pt-4 border-t border-stone-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-400">
                        🔑 Tài khoản Chủ Shop đã khởi tạo sẵn
                    </span>
                    <span class="text-[10px] bg-stone-800 text-stone-300 px-2 py-0.5 rounded-full">Click để điền nhanh</span>
                </div>

                <div class="space-y-2 text-xs">
                    <button type="button" onclick="fillAccount('admin@luminabeauty.vn', 'Admin@123456')"
                        class="w-full text-left p-3 rounded-2xl bg-stone-950/90 hover:bg-stone-800/80 border border-stone-800 hover:border-rose-500/40 transition flex items-center justify-between group">
                        <div>
                            <p class="font-semibold text-stone-200 group-hover:text-rose-300">1. Tài khoản Admin Mặc định</p>
                            <p class="font-mono text-[11px] text-stone-400">Email: <strong class="text-white">admin@luminabeauty.vn</strong> | Pass: <strong class="text-rose-400">Admin@123456</strong></p>
                        </div>
                        <span class="text-[11px] font-bold text-rose-400">Chọn →</span>
                    </button>

                    <button type="button" onclick="fillAccount('phongdev3979@gmail.com', 'Admin@123456')"
                        class="w-full text-left p-3 rounded-2xl bg-stone-950/90 hover:bg-stone-800/80 border border-stone-800 hover:border-rose-500/40 transition flex items-center justify-between group">
                        <div>
                            <p class="font-semibold text-stone-200 group-hover:text-rose-300">2. Tài khoản Chủ Shop Nguyễn Phong</p>
                            <p class="font-mono text-[11px] text-stone-400">Email: <strong class="text-white">phongdev3979@gmail.com</strong> | Pass: <strong class="text-rose-400">Admin@123456</strong></p>
                        </div>
                        <span class="text-[11px] font-bold text-rose-400">Chọn →</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex items-center justify-between text-xs text-stone-400 px-2">
            <a href="{{ route('home') }}" class="hover:text-white transition">← Về trang bán hàng</a>
            <a href="{{ route('orders.track') }}" class="text-rose-400 hover:underline">Khách tra cứu đơn mua (/my-orders) →</a>
        </div>
    </div>

    <script>
        function fillAccount(email, password) {
            document.getElementById('email-input').value = email;
            document.getElementById('password-input').value = password;
        }
    </script>
</body>
</html>
