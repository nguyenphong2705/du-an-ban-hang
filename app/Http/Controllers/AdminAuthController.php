<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_admin) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản này không có quyền Quản trị Chủ Shop (Admin).',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->route('admin.index')
                ->with('success', "Chào mừng Chủ Shop {$user->name} đã đăng nhập vào hệ thống quản trị!");
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu Chủ Shop không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Đã đăng xuất khỏi phiên làm việc Chủ Shop.');
    }
}
