<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\University;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Condition;

class SuperAdminController extends Controller
{
    // ==========================================
    // 1. หน้า Dashboard รวมทุกมหาวิทยาลัย (อัปเกรดสถิติ)
    // ==========================================
    public function index()
    {
        if (Auth::user()->role !== 'super_admin') abort(403, 'พื้นที่หวงห้ามเฉพาะเจ้าของระบบเท่านั้น');

        $universities = University::orderBy('created_at', 'desc')->get();

        $globalStats = [
            'total_users' => 0,
            'total_products' => 0,
            'total_sales' => 0,
        ];

        foreach ($universities as $uni) {
            $uni->run(function () use (&$globalStats) {
                $globalStats['total_users'] += \App\Models\User::count();
                $globalStats['total_products'] += \App\Models\Product::count();
                $globalStats['total_sales'] += \App\Models\Transaction::where('payment_status', 'completed')->sum('amount');
            });
        }

        return view('super.dashboard', compact('universities', 'globalStats'));
    }

    // ==========================================
    // 2. สลับสถานะ (เปิด/ปิด) การใช้งานมหาลัย
    // ==========================================
    public function toggleStatus(University $university)
    {
        if (Auth::user()->role !== 'super_admin') abort(403);


        $university->update(['is_active' => !$university->is_active]);

        $statusName = $university->is_active ? 'เปิดการใช้งาน' : 'ระงับการใช้งาน';
        return back()->with('success', '🔄 ' . $statusName . ' ' . $university->name . ' เรียบร้อยแล้ว!');
    }

    // ==========================================
    //  ฟังก์ชันสร้างมหาวิทยาลัยใหม่ (Onboarding New Tenant)
    // ==========================================
    public function store(Request $request)
    {   
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:universities,domain',
            'primary_color' => 'required|string|max:7',
        ]);

        $newUniversity = University::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->name,
            'domain' => $request->domain,
            'primary_color' => $request->primary_color,
            'is_active' => true,
        ]);

        $newUniversity->domains()->create(['domain' => $request->domain]);

        // ==========================================
        //  แจก Starter Pack ให้มหาลัยใหม่ทันที!
        // ==========================================
        $newUniversity->run(function () {
            $defaultCategories = ['อุปกรณ์การเรียน', 'หนังสือ/ชีทเรียน', 'อุปกรณ์ไอที', 'เสื้อผ้านักศึกษา', 'หอพัก/ของใช้', 'อื่นๆ'];
            foreach ($defaultCategories as $index => $cat) {
                \App\Models\Category::create([
                    'name' => $cat,
                    'slug' => 'cat-' . ($index + 1)
                ]);
            }

            $defaultConditions = ['มือหนึ่ง (ของใหม่)', 'มือสอง (สภาพนางฟ้า)', 'มือสอง (สภาพใช้งาน)'];
            foreach ($defaultConditions as $index => $cond) {
                \App\Models\Condition::create([
                    'name' => $cond,
                    'slug' => 'cond-' . ($index + 1)
                ]);
            }
        });

        return back()->with('success', '🎉 สร้างมหาวิทยาลัยใหม่ พร้อมชุดหมวดหมู่พื้นฐานสำเร็จ!');
    }

    // ==========================================
    //  ฟังก์ชันสร้างผู้ดูแลประจำมหาวิทยาลัย (Uni Admin)
    // ==========================================
    public function createAdmin(Request $request, University $university)
    {
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $exists = \App\Models\User::where('university_id', $university->id)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return back()->withErrors(['email' => '❌ อีเมลนี้มีคนใช้งานแล้วใน ' . $university->name]);
        }

        $university->run(function () use ($request) {
            \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'uni_admin',
            ]);
        });

        return back()->with('success', '🎉 สร้างบัญชีผู้ดูแล (Uni Admin) ให้ ' . $university->name . ' สำเร็จ!');
    }
}
