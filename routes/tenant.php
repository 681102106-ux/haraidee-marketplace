<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UniAdminController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\CheckTenantActive::class,
])->group(function () {

    // ==========================================
    // 1. PUBLIC ROUTES (หน้าแรกของมหาลัย = หน้าตลาดนัด)
    // ==========================================
    Route::get('/', [ProductController::class, 'index'])->name('products.index'); // 🔥 แก้ตรงนี้กลับมาเป็น ProductController ครับ

    // ==========================================
    // 2. AUTH ROUTES (Login/Register)
    // ==========================================
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    });

    // ==========================================
    // 3. PROTECTED ROUTES (ต้อง Login)
    // ==========================================
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // --> จัดการสินค้า
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/my-products', [ProductController::class, 'myProducts'])->name('products.my_products');
        Route::get('/products/{product}/checkout', [ProductController::class, 'checkout'])->name('products.checkout');

        // --> Chat & บิลชำระเงิน
        Route::get('/inbox', [ConversationController::class, 'index'])->name('chat.index');
        Route::post('/chat/start/{product}', [ConversationController::class, 'startChat'])->name('chat.start');
        Route::get('/chat/{conversation}', [ConversationController::class, 'show'])->name('chat.show');
        Route::post('/chat/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('chat.sendMessage');
        Route::post('/chat/{conversation}/bill', [ConversationController::class, 'sendBill'])->name('chat.bill.send');

        // --> Profile & Wishlist
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar/delete', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
        Route::get('/wishlists', [WishlistController::class, 'index'])->name('wishlists.index');
        Route::post('/wishlists/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlists.toggle');

        // --> Transactions & Reviews
        Route::post('/products/{product}/offers/send', [ProductController::class, 'sendOffer'])->name('products.offers.send');
        Route::post('/products/{product}/offers/cancel', [ProductController::class, 'cancelOffer'])->name('products.offers.cancel');
        Route::post('/products/{product}/sold', [ProductController::class, 'markAsSold'])->name('products.sold');
        Route::post('/transactions/{transaction}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

        // ==========================================
        // UNI ADMIN
        // ==========================================
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard', [UniAdminController::class, 'index'])->name('admin.dashboard');
            Route::post('/categories', [UniAdminController::class, 'storeCategory'])->name('admin.categories.store');
            Route::delete('/categories/{category}', [UniAdminController::class, 'destroyCategory'])->name('admin.categories.destroy');
            Route::post('/conditions', [UniAdminController::class, 'storeCondition'])->name('admin.conditions.store');
            Route::delete('/conditions/{condition}', [UniAdminController::class, 'destroyCondition'])->name('admin.conditions.destroy');
            Route::patch('/products/{product}/toggle-status', [UniAdminController::class, 'toggleProductStatus'])->name('admin.products.toggle_status');
        });
    });

    // ==========================================
    // DYNAMIC ROUTE (ต้องอยู่ล่างสุดเสมอ)
    // ==========================================
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
});
