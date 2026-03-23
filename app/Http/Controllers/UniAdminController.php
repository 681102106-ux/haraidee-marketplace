<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UniAdminController extends Controller
{
    // ==========================================
    // 1. หน้า Dashboard สรุปสถิติ & จัดการหมวดหมู่
    // ==========================================
    public function index()
    {
        if (Auth::user()->role !== 'uni_admin') {
            abort(403, '⛔ พื้นที่สำหรับผู้ดูแลมหาวิทยาลัยเท่านั้น');
        }

        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'total_sales' => Transaction::where('payment_status', 'completed')->sum('amount'),
        ];

       $categories = Category::withCount('products') 
            ->withCount(['products as active_count' => function ($query) {
                $query->whereIn('status', ['active', 'reserved']);
            }])->get();

        $conditions = Condition::withCount('products')
            ->withCount(['products as active_count' => function ($query) {
                $query->whereIn('status', ['active', 'reserved']);
            }])->get();

            $recent_products = Product::with(['user', 'category'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'categories', 'conditions', 'recent_products'));
    }

    // ==========================================
    // 2. สร้างหมวดหมู่ใหม่
    // ==========================================
    public function storeCategory(Request $request)
    {
        if (Auth::user()->role !== 'uni_admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time()
        ]);

        return back()->with('success', '✅ เพิ่มหมวดหมู่ใหม่สำเร็จ!');
    }

    // ==========================================
    // 3. สร้างสภาพสินค้าใหม่   
    // ==========================================

    public function storeCondition(Request $request)
    {
        if (Auth::user()->role !== 'uni_admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:conditions,name'
        ]);

        Condition::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time()
        ]);

        return back()->with('success', '✅ เพิ่มสภาพสินค้าใหม่สำเร็จ!');
    }

    // ==========================================
    // 4. ลบหมวดหมู่
    // ==========================================
    public function destroyCategory(Category $category)
    {
        if (Auth::user()->role !== 'uni_admin') abort(403);


        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => '❌ ไม่สามารถลบได้ เพราะมีสินค้าใช้งานหมวดหมู่นี้อยู่']);
        }

        $category->delete();

        return back()->with('success', '🗑️ ลบหมวดหมู่เรียบร้อยแล้ว!');
    }

    // ==========================================
    // 5. ลบสภาพสินค้า
    // ==========================================
    public function destroyCondition(Condition $condition)
    {
        if (Auth::user()->role !== 'uni_admin') abort(403);


        if ($condition->products()->count() > 0) {
            return back()->withErrors(['error' => '❌ ไม่สามารถลบได้ เพราะมีสินค้าใช้งานสภาพสินค้า nàyอยู่']);
        }

        $condition->delete();

        return back()->with('success', '🗑️ ลบสภาพสินค้าเรียบร้อยแล้ว!');
    }

    // ==========================================
    // 6. แบน / ปลดแบน สินค้า (Moderation)
    // ==========================================
    public function toggleProductStatus(Product $product)
    {
        if (Auth::user()->role !== 'uni_admin') abort(403);

        if ($product->status === 'banned') {
            $product->update(['status' => 'active']);
            return back()->with('success', '🟢 ปลดแบนสินค้าเรียบร้อยแล้ว');
        } else {
            // ถ้าระงับการขาย ให้ลบบิลที่ค้างอยู่ (pending) ทิ้งด้วย เพื่อไม่ให้คนซื้อโอนเงินเก้อ
            \App\Models\Transaction::where('product_id', $product->id)
                                   ->where('payment_status', 'pending')
                                   ->delete();
                                   
            $product->update(['status' => 'banned']);
            return back()->with('success', '🔴 ระงับการขายสินค้าเรียบร้อยแล้ว');
        }
    }
}
