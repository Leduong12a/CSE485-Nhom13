<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản của bạn hiện đã bị khóa. Vui lòng liên hệ Admin.']);
            }

            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đã đăng xuất tài khoản thành công.');
    }

    private function redirectUser($user)
    {
        if ($user->role === 'MANAGER') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'STAFF') {
            return redirect()->route('staff.workdesk');
        }
        return redirect()->route('home');
    }
}
