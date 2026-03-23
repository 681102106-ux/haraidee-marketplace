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
        // 1. ตรวจสอบข้อมูลที่รับมาจากฟอร์ม
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255', 
            // หมายเหตุ: เรายังไม่ใส่ unique:users เพราะเราใช้ Composite Index (university_id + email)
            'password' => 'required|min:6|confirmed', 
        ]);

        // 2. ตรวจสอบว่าอีเมลนี้ซ้ำในมหาวิทยาลัยนี้หรือไม่?
        $exists = User::where('email', $request->email)->exists();
        if ($exists) {
            return back()->withErrors(['email' => 'อีเมลนี้ถูกใช้งานแล้วในมหาวิทยาลัยนี้'])->withInput();
        }

        // 3. สร้าง User ลง Database (university_id จะถูกใส่ให้อัตโนมัติจาก Trait)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, 
            'role' => 'student', 
        ]);

        
        Auth::login($user);

        // 5. ส่งไปหน้า Feed
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

        // Auth::attempt จะเช็กอีเมล+รหัสผ่าน และเช็ก university_id ให้อัตโนมัติ
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // ป้องกันขโมย Session
            
            // 🔥 หัวใจหลัก: เช็ก Role แล้วแยกทางเดิน!
            $role = Auth::user()->role;

            if ($role === 'uni_admin') {
                return redirect()->route('admin.dashboard'); // ไปหน้าแอดมินมหาลัย
            } elseif ($role === 'super_admin') {
                return redirect()->route('super.dashboard'); // ไปหน้าแอดมินระบบหลัก
            }

            // ถ้าเป็น student ให้ไปหน้า Feed ตลาดนัด
            return redirect()->route('products.index');
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('products.index');
    }
}