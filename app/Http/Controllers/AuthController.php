<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
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

            return redirect()->intended(route(Auth::user()->homeRoute()));
        }

        return back()
            ->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /** สมัครสมาชิก: ได้บทบาท "ลูกค้า" เท่านั้น */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)->mixedCase()->numbers()->symbols()],
        ]);

        User::create($data + ['role' => User::ROLE_CUSTOMER]);

        // ไม่ล็อกอินให้อัตโนมัติ: แจ้งว่าสมัครสำเร็จ แล้วให้ไปเข้าสู่ระบบเอง
        return redirect()->route('login')
            ->with('success', 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบเพื่อใช้งาน');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
