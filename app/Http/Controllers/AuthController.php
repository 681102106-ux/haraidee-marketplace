<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==========================================
    // 1. ระบบสมัครสมาชิก (Register)
    // ==========================================
    public function showRegisterForm()
    {
        return view('auth.register'); 
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255', 
            'password' => 'required|min:6|confirmed', 
        ]);

        $exists = User::where('email', $request->email)->exists();
        if ($exists) {
            return back()->withErrors(['email' => 'อีเมลนี้ถูกใช้งานแล้วในมหาวิทยาลัยนี้'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, 
            'role' => 'student', 
        ]);

        Auth::login($user);

        return redirect()->route('products.index');
    }

    // ==========================================
    // 2. ระบบเข้าสู่ระบบ (Login) และแยก Dashboard
    // ==========================================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); 
            
            $role = Auth::user()->role;

            if ($role === 'super_admin') {
                return redirect()->route('super.dashboard');
            } elseif ($role === 'uni_admin') {
                return redirect()->route('admin.dashboard'); 
            }

            if (tenant()) {
                return redirect()->route('products.index');
            } else {
                return redirect()->route('central.welcome');
            }
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    // ==========================================
    // 3. ระบบออกจากระบบ (Logout)
    // ==========================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if (tenant()) {
            return redirect()->route('products.index');
        } else {
            return redirect()->route('central.welcome');
        }
    }
}