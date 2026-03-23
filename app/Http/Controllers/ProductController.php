<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\User;

class ProductController extends Controller
{
    // ==========================================
    //  หน้า "สินค้าของฉัน" (My Products)
    // ==========================================
    public function myProducts()
    {
        $products = Product::with(['conversations.buyer'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('products.my_products', compact('products'));
    }

    // ==========================================
    //  ฟังก์ชันเปลี่ยนสถานะเป็น "ขายแล้ว"
    // ==========================================
   public function markAsSold(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) abort(403, 'คุณไม่มีสิทธิ์แก้ไขสินค้านี้');

        \Illuminate\Support\Facades\DB::transaction(function () use ($product) {

            $product->update(['status' => 'sold']);

            $transaction = \App\Models\Transaction::where('product_id', $product->id)
                                                  ->where('payment_status', 'pending')
                                                  ->first();
            if ($transaction) {
                $transaction->update(['payment_status' => 'completed']);
            }
        });

        return back()->with('success', '🎉 ยืนยันปิดการขายสำเร็จ ยอดเงินเข้าสู่สถิติระบบแล้ว!');
    }
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('products.create', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'images' => 'required|array|max:5', 
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', 
        ]);

        DB::transaction(function () use ($validated, $request) {

            $product = Product::create([
                'user_id' => Auth::id(),
                'category_id' => $validated['category_id'],
                'condition_id' => $validated['condition_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'status' => 'active',
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)
                        ->toMediaCollection('product_images');
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'ลงขายสินค้าเรียบร้อยแล้ว!');
    }

    // ==========================================
    // หน้า Feed ตลาดนัด (อัปเกรดระบบ Search & Filter)
    // ==========================================
    public function index(Request $request)
    {
        $query = Product::with(['media', 'category', 'user'])->where('status', 'active');

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        });

        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->where('category_id', $request->category);
        });

        $products = $query->orderByDesc('is_boosted')->latest()->paginate(20)->appends($request->query());

        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    // ==========================================
    // แสดงหน้ารายละเอียดสินค้า 1 ชิ้น (Product Detail)
    // ==========================================
    public function show(Product $product)
    {
        $viewKey = 'viewed_product_' . $product->id;

        if (!session()->has($viewKey)) {
            $product->increment('views_count');
            session()->put($viewKey, true);
        }

        $product->load(['media', 'category', 'condition', 'user']);

        return view('products.show', compact('product'));
    }

    // ==========================================
    // แสดงหน้าฟอร์ม "แก้ไข" สินค้า
    // ==========================================
    public function edit(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'คุณไม่ใช่เจ้าของสินค้านี้');
        }

        $categories = Category::all();
        $conditions = Condition::all();

        return view('products.edit', compact('product', 'categories', 'conditions'));
    }

    // ==========================================
    // บันทึกข้อมูลที่แก้ไข
    // ==========================================
    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'คุณไม่ใช่เจ้าของสินค้านี้');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', 
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request, $product) {
            $product->update([
                'category_id' => $validated['category_id'],
                'condition_id' => $validated['condition_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
            ]);

            if ($request->hasFile('images')) {
                $product->clearMediaCollection('product_images'); 
                
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)->toMediaCollection('product_images'); 
                }
            }
        });

        return redirect()->route('products.my_products')->with('success', '✏️ แก้ไขสินค้าเรียบร้อยแล้ว!');
    }

    // ==========================================
    //  ลบสินค้า (Soft Delete)
    // ==========================================
    public function destroy(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'คุณไม่ใช่เจ้าของสินค้านี้');
        }

        $product->delete();

        return back()->with('success', '🗑️ ลบสินค้าออกจากระบบแล้ว!');
    }

    // ==========================================
    //  หน้าจอชำระเงิน (PromptPay Checkout)
    // ==========================================
  public function checkout(Product $product)
    {
        $transaction = \App\Models\Transaction::where('product_id', $product->id)
                                              ->where('payment_status', 'pending')
                                              ->first();

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            abort(403, '⛔ บิลเรียกเก็บเงินนี้ไม่ได้ส่งถึงคุณ หรือถูกยกเลิกไปแล้วครับ');
        }

        $seller = $product->user;

        return view('products.checkout', compact('product', 'seller'));
    }

    // ==========================================
    // คนขายส่งบิล (ล็อกสินค้าให้คนซื้อคนนี้)
    // ==========================================
   public function sendOffer(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) abort(403);
        if ($product->status !== 'active') return back()->withErrors(['error' => 'สินค้านี้ติดจองหรือขายไปแล้ว']);

        $request->validate(['buyer_id' => 'required|exists:users,id']);

        \Illuminate\Support\Facades\DB::transaction(function () use ($product, $request) {
            $product->update(['status' => 'reserved']);

            \App\Models\Transaction::create([
                'user_id' => $request->buyer_id, 
                'product_id' => $product->id,
                'amount' => $product->price,
                'payment_status' => 'pending', 
            ]);
        });

        $buyer = \App\Models\User::find($request->buyer_id);
        return back()->with('success', '🧾 ส่งบิลให้ ' . $buyer->name . ' สำเร็จ! (สินค้าถูกล็อกแล้ว)');
    }

    // ==========================================
    //  คนขายยกเลิกบิล (ปลดล็อกสินค้า)
    // ==========================================
    public function cancelOffer(Product $product)
    {
        if ($product->user_id !== Auth::id()) abort(403);

        \Illuminate\Support\Facades\DB::transaction(function () use ($product) {
            $product->update(['status' => 'active']);

            \App\Models\Transaction::where('product_id', $product->id)
                                   ->where('payment_status', 'pending')
                                   ->delete();
        });

        return back()->with('success', '🔓 ยกเลิกบิลและปลดล็อกสินค้าเรียบร้อยแล้ว');
    }
}
