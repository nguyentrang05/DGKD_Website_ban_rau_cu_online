<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hien thi trang dang nhap
    public function login()
    {
        return view('auth.login');
    }

    // Xu ly dang nhap
    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $account = Auth::user();

            if ($account->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($account->isUser()) {
                return redirect()->route('home');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'Tài khoản không có quyền truy cập.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Sai email hoặc mật khẩu.',
        ])->withInput();
    }

    // Hien thi trang dang ky
    public function register()
    {
        return view('auth.register');
    }
    // Xu ly dang ky
    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $account = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // mặc định người đăng ký là user
        ]);

        Auth::login($account); // tự động đăng nhập sau khi đăng ký

        return redirect()->route('home'); // chuyển về trang user
    }


    // Xu ly dang xuat
    public function logout()
    {
        // Logic xu ly dang xuat
        Auth::logout();
        return redirect()->route('login');
    }
}
