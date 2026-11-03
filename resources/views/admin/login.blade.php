<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị Viên Shop | Lumina Beauty Admin</title>
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
<body class="font-sans antialiased min-h-screen bg-stone-950 text-stone-100 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 relative overflow-x-hidden">
    <div class="fixed -top-40 -left-40 w-96 h-96 rounded-full bg-rose-500/15 blur-3xl pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

    <div class="max-w-lg w-full mx-auto space-y-6 relative z-10">
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
                        Shop Administrator Portal
                    </p>
                </div>
            </a>
            <h1 class="font-serif text-2xl font-bold text-white pt-2">
                Đăng Nhập Quản Trị Viên Của Shop
            </h1>
            <p class="text-xs text-stone-400">
                Tra cứu đơn hàng, thêm/sửa/xóa sản phẩm & thống kê bán chạy, tồn kho, hết hàng
            </p>
        </div>

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
                        Email hoặc Tên đăng nhập Quản trị viên
                    </label>
                    <input type="text" id="email-input" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Nhập email hoặc tên đăng nhập quản trị"
                        class="w-full px-4 py-3 text-sm rounded-2xl bg-stone-950 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-300 mb-2">
                        Mật khẩu
                    </label>
                    <input type="password" id="password-input" name="password" required
                        placeholder="••••••••••••"
                        class="w-full px-4 py-3 text-sm rounded-2xl bg-stone-950 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <div class="flex items-center justify-between text-xs text-stone-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded accent-rose-500">
                        <span>Ghi nhớ phiên Quản trị viên</span>
                    </label>
                    <span class="text-rose-400 font-medium">Bảo mật Role Admin</span>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold py-3.5 rounded-2xl shadow-lg shadow-rose-500/25 transition text-sm">
                    Đăng Nhập Trang Quản Trị Viên (/admin) →
                </button>
            </form>
        </div>

        <div class="flex items-center justify-between text-xs text-stone-400 px-2">
            <a href="{{ route('home') }}" class="hover:text-white transition">← Về trang chủ cửa hàng</a>
            <a href="{{ route('customer.login') }}" class="text-rose-400 hover:underline">Bạn là Khách mua hàng? Đăng nhập tại /login →</a>
        </div>
    </div>
</body>
</html>
