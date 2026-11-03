<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($validated['email']);
        $remember = $request->boolean('remember', true);

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if ($user && Hash::check($validated['password'], $user->password)) {
            if (!$user->is_admin) {
                return back()->withErrors([
                    'email' => 'Tài khoản này là tài khoản Khách hàng, không có quyền Quản trị viên Chủ Shop. Vui lòng đăng nhập tại Trang Khách Hàng (/login).',
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Đã đăng xuất khỏi phiên làm việc Quản trị viên.');
    }
}
