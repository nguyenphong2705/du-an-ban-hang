<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Class AdminAuthController
 *
 * Xử lý xác thực đăng nhập và phiên làm việc dành riêng cho Quản trị viên (Chủ Shop):
 * - Hiển thị giao diện đăng nhập Admin riêng biệt tại đường dẫn /quan-tri/dang-nhap.
 * - Kiểm tra thông tin tài khoản và cờ phân quyền quản trị viên (`is_admin = true`).
 * - Đăng xuất và hủy phiên làm việc an toàn, bảo vệ chống CSRF.
 */
class AdminAuthController extends Controller
{
    /**
     * Hiển thị giao diện trang đăng nhập dành cho Quản trị viên.
     *
     * Chức năng:
     * - Nếu quản trị viên đã đăng nhập từ trước, tự động chuyển hướng đến bảng điều khiển (/quan-tri).
     * - Trả về view đăng nhập trang quản trị.
     *
     * @return View|RedirectResponse Giao diện đăng nhập hoặc chuyển hướng đến /quan-tri
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    /**
     * Xử lý xác thực đăng nhập của Quản trị viên.
     *
     * Quy trình kiểm tra:
     * 1. Xác thực dữ liệu đầu vào (email/username và mật khẩu).
     * 2. Tìm tài khoản trong database và đối chiếu mật khẩu đã mã hóa bằng Hash::check().
     * 3. Kiểm tra quyền hạn `is_admin`: nếu là tài khoản khách hàng thông thường, từ chối quyền truy cập.
     * 4. Tạo lại Session ID để chống tấn công Session Fixation và ghi nhớ đăng nhập nếu được chọn.
     *
     * @param Request $request Yêu cầu HTTP chứa 'email', 'password', và tùy chọn 'remember'
     * @return RedirectResponse Chuyển hướng đến bảng điều khiển Admin hoặc quay lại kèm thông báo lỗi
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($validated['email']);
        if (strcasecmp($loginInput, 'admin@shinebeauty.vn') === 0) {
            $loginInput = 'admin@luminabeauty.vn';
        }
        $remember = $request->boolean('remember', true);

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if ($user && Hash::check($validated['password'], $user->password)) {
            // Kiểm tra quyền Quản trị viên
            if (!$user->is_admin) {
                return back()->withErrors([
                    'email' => 'Tài khoản này là tài khoản Khách hàng, không có quyền Quản trị viên Chủ Shop. Vui lòng đăng nhập tại Trang Khách Hàng (/dang-nhap).',
                ])->onlyInput('email');
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->route('admin.index')
                ->with('success', "Chào mừng Quản trị viên {$user->name} đã đăng nhập vào hệ thống quản trị Shop!");
        }

        return back()->withErrors([
            'email' => 'Email / Tên đăng nhập hoặc mật khẩu Quản trị viên không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất Quản trị viên và xóa phiên làm việc.
     *
     * Chức năng:
     * - Đăng xuất người dùng hiện tại khỏi Auth guard.
     * - Hủy phiên làm việc session (invalidate) và tạo lại CSRF token mới (regenerateToken).
     * - Chuyển hướng về trang đăng nhập Quản trị viên kèm thông báo thành công.
     *
     * @param Request $request Yêu cầu HTTP
     * @return RedirectResponse Chuyển hướng về trang đăng nhập /quan-tri/dang-nhap
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Đã đăng xuất khỏi phiên làm việc Quản trị viên.');
    }
}
