<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hộp thư đến - {{ $email }} - Gmail</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="https://ssl.gstatic.com/ui/v1/icons/mail/rfr/gmail.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background-color: #F6F8FC; color: #202124; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- =====================================================================
         THANH HEADER HỘP THƯ GMAIL (GMAIL WEBMAIL HEADER)
         ===================================================================== -->
    <header class="bg-[#F6F8FC] border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full hover:bg-gray-200/70 flex items-center justify-center text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xl font-medium text-gray-700 tracking-tight">Gmail</span>
                <span class="text-xs bg-red-100 text-red-700 font-medium px-2.5 py-0.5 rounded-full">Hộp thư đến</span>
            </div>
        </div>

        <!-- Thanh tìm kiếm trong hộp thư -->
        <div class="hidden md:flex flex-1 max-w-xl bg-[#EAF1FB] rounded-full px-4 py-2 items-center gap-3 text-sm text-gray-600">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>from:SHINE Beauty to:{{ $email }}</span>
        </div>

        <!-- Thông tin tài khoản Gmail đang mở -->
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-gray-800">{{ $user->name }}</p>
                <p class="text-[11px] text-gray-500">{{ $email }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-sm flex items-center justify-center shadow-xs">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        </div>
    </header>

    <!-- =====================================================================
         NỘI DUNG HỘP THƯ ĐẾN GMAIL (SIDEBAR + NỘI DUNG EMAIL XÁC THỰC)
         ===================================================================== -->
    <div class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Cột Trái: Danh mục Hộp thư Gmail -->
        <aside class="lg:col-span-3 space-y-2">
            <a href="{{ route('customer.profile') }}"
               class="inline-flex items-center gap-2 w-full justify-center bg-[#C2E7FF] hover:bg-[#b3dffc] text-[#001D35] font-medium text-sm px-4 py-3 rounded-2xl shadow-2xs transition">
                ← Quay lại trang Xác thực
            </a>

            <nav class="pt-3 space-y-1 text-sm">
                <div class="flex items-center justify-between px-4 py-2 rounded-full bg-[#D3E3FD] text-[#041E49] font-bold">
                    <span class="flex items-center gap-2.5">📥 Hộp thư đến</span>
                    <span class="text-xs">{{ $otpSession ? '1' : '0' }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2 rounded-full text-gray-600 hover:bg-gray-200/60">
                    <span class="flex items-center gap-2.5">⭐ Có gắn dấu sao</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2 rounded-full text-gray-600 hover:bg-gray-200/60">
                    <span class="flex items-center gap-2.5">📨 Đã gửi</span>
                </div>
                <div class="flex items-center justify-between px-4 py-2 rounded-full text-gray-600 hover:bg-gray-200/60">
                    <span class="flex items-center gap-2.5">🛡️ Thư rác (Spam)</span>
                </div>
            </nav>
        </aside>

        <!-- Cột Phải: Chi tiết Email nhận được -->
        <main class="lg:col-span-9 bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            @if($otpSession && !empty($otpSession['code']))
                <!-- Tiêu đề thư (Email Subject Header) -->
                <div class="p-6 sm:p-8 border-b border-gray-100 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-lg sm:text-xl font-normal text-gray-900">
                                [SHINE Beauty] Mã xác thực Email của bạn: <strong class="font-mono">{{ $otpSession['code'] }}</strong>
                            </h1>
                            <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">Hộp thư đến</span>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $otpSession['sent_at'] ?? now()->format('H:i d/m/Y') }} (vừa xong)
                        </span>
                    </div>

                    <!-- Thông tin Người gửi & Người nhận -->
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#4A947A] text-white font-bold flex items-center justify-center text-sm">
                                SB
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-bold text-sm text-gray-900">SHINE Beauty Cosmetics</span>
                                    <span class="text-xs text-gray-500">&lt;no-reply@shinebeauty.vn&gt;</span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    tới <strong>{{ $user->name }}</strong> &lt;{{ $email }}&gt;
                                </p>
                            </div>
                        </div>

                        <button type="button"
                            onclick="copyOtpCode('{{ $otpSession['code'] }}')"
                            id="copy-otp-btn"
                            class="text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-full shadow-xs transition cursor-pointer">
                            📋 Sao chép mã {{ $otpSession['code'] }}
                        </button>
                    </div>
                </div>

                <!-- Nội dung HTML của thư -->
                <div class="p-6 sm:p-8 bg-[#FAF9F6]/50">
                    <div style="font-family: 'Helvetica Neue', Arial, sans-serif; max-width: 560px; margin: 0 auto; background-color: #FAF9F6; padding: 32px 24px; border-radius: 20px; border: 1px solid #E2ECE7; color: #292524;">
                        <div style="text-align: center; margin-bottom: 24px;">
                            <span style="display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #6B8E7B; background: #E8F2EC; padding: 6px 14px; border-radius: 999px;">
                                SHINE Beauty • Xác Thực Tài Khoản
                            </span>
                            <h2 style="margin: 12px 0 4px; font-size: 22px; color: #1C1917;">Mã Xác Thực Địa Chỉ Email</h2>
                            <p style="margin: 0; font-size: 13px; color: #78716C;">Gửi tới hộp thư Gmail: <strong>{{ $email }}</strong></p>
                        </div>

                        <div style="background-color: #FFFFFF; border-radius: 16px; padding: 24px; border: 1px solid #E7E5E4; text-align: center;">
                            <p style="margin: 0 0 14px; font-size: 14px; color: #44403C; line-height: 1.6;">
                                Xin chào <strong>{{ $user->name }}</strong>,<br>
                                Bạn vừa yêu cầu xác thực địa chỉ Email cho tài khoản khách hàng tại <strong>SHINE Beauty</strong>. Đây là mã xác nhận 6 chữ số của bạn:
                            </p>

                            <div style="margin: 20px auto; display: inline-block; background-color: #FDF5F2; border: 2px dashed #C85A32; border-radius: 14px; padding: 14px 32px;">
                                <span style="font-family: 'Courier New', monospace; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #B04A25;">
                                    {{ $otpSession['code'] }}
                                </span>
                            </div>

                            <p style="margin: 14px 0 18px; font-size: 12px; color: #78716C;">
                                ⏳ Mã xác thực có hiệu lực trong vòng <strong>15 phút</strong>. Vui lòng không chia sẻ mã này cho bất kỳ ai.
                            </p>

                            <!-- Nút xác nhận trực tiếp ngay từ trong thư Gmail -->
                            <form action="{{ route('customer.email.verify') }}" method="POST" style="margin-top: 12px;">
                                @csrf
                                <input type="hidden" name="email_otp" value="{{ $otpSession['code'] }}">
                                <button type="submit" style="background-color: #4A947A; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 12px 28px; border-radius: 999px; border: none; cursor: pointer;">
                                    ✓ Xác Thực Ngay Cho Email {{ $email }}
                                </button>
                            </form>
                        </div>

                        <p style="margin: 20px 0 0; text-align: center; font-size: 12px; color: #A8A29E;">
                            © SHINE Beauty Cosmetics — Chăm sóc làn da Việt chính hãng.
                        </p>
                    </div>
                </div>
            @else
                <div class="p-14 text-center space-y-4">
                    <p class="text-base font-medium text-gray-700">Hộp thư đến của {{ $email }} hiện chưa có thư xác thực mới.</p>
                    <form action="{{ route('customer.email.send-otp') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-5 py-2.5 rounded-full transition cursor-pointer">
                            📨 Gửi mã xác nhận mới về {{ $email }}
                        </button>
                    </form>
                </div>
            @endif
        </main>
    </div>

    <script>
        function copyOtpCode(code) {
            navigator.clipboard.writeText(code);
            const btn = document.getElementById('copy-otp-btn');
            if (btn) {
                btn.textContent = '✓ Đã sao chép: ' + code;
            }
        }
    </script>
</body>
</html>
