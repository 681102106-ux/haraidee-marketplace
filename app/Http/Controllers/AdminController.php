<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ==========================================
    // 1. หน้า Admin Dashboard ของมหาวิทยาลัยนั้นๆ
    // ==========================================
    public function dashboard()
    {
        // ตรวจสอบสิทธิ์ว่าต้องเป็น uni_admin เท่านั้น
        if (Auth::user()->role !== 'uni_admin') {
            abort(403, 'พื้นที่เฉพาะผู้ดูแลระบบมหาวิทยาลัยเท่านั้น');
        }

        // ดึงสินค้าทั้งหมดในมหาวิทยาลัยนี้ (Global Scope ช่วยกรองให้อัตโนมัติ!)
        $products = Product::with(['user', 'category'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        return view('admin.dashboard', compact('products'));
    }

    // ==========================================
    // 2. ฟังก์ชันแบนสินค้า (Banned)
    // ==========================================
    public function banProduct(Product $product)
    {
        if (Auth::user()->role !== 'uni_admin') {
            abort(403, 'ไม่มีสิทธิ์แบนสินค้า');
        }

        // เปลี่ยนสถานะเป็น banned
        $product->update(['status' => 'banned']);

        return back()->with('success', 'แบนสินค้าเรียบร้อยแล้ว');
    }
}