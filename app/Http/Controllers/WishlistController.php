<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // 1. หน้าแสดงสินค้าที่ฉันกดเซฟไว้ทั้งหมด
    public function index()
    {
        // ดึงสินค้าจากความสัมพันธ์ wishlists() ของคนที่ล็อกอินอยู่
        $products = Auth::user()->wishlists()->with(['media'])->latest()->get();
        return view('wishlists.index', compact('products'));
    }

    // 2. ฟังก์ชันสลับสถานะ (กดครั้งแรก=เซฟ, กดซ้ำ=เอาออก)
    public function toggle(Product $product)
    {
        Auth::user()->wishlists()->toggle($product->id);

        return back()->with('success', '❤️ อัปเดตรายการโปรดของคุณแล้ว!');
    }
}
