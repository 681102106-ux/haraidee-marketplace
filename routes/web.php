<?php

use Illuminate\Support\Facades\Route;
use App\Models\University;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AuthController;

$centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);

foreach ($centralDomains as $domain) {
    Route::domain($domain)->group(function () {

        Route::get('/', function () {
            $universities = University::where('is_active', true)->get();
            $backdoorUni = University::first();
            return view('welcome', compact('universities', 'backdoorUni'));
        })->name('central.welcome');

        // ==========================================
        // 👑 ประตูทางเข้า SUPER ADMIN (ล็อกอินที่ส่วนกลาง)
        // ==========================================
        Route::middleware('guest')->group(function () {
            Route::get('/super-login', [AuthController::class, 'showLoginForm'])->name('super.login');
            Route::post('/super-login', [AuthController::class, 'login']);
        });

        Route::middleware('auth')->group(function () {
            Route::post('/super-logout', [AuthController::class, 'logout'])->name('super.logout');

            // 👑 หน้าต่างควบคุม Super Admin Dashboard
            Route::get('/super/dashboard', [SuperAdminController::class, 'index'])->name('super.dashboard');
            Route::post('/super/universities', [SuperAdminController::class, 'store'])->name('super.universities.store');
            Route::post('/super/universities/{university}/admins', [SuperAdminController::class, 'createAdmin'])->name('super.universities.admins.store');
            Route::patch('/super/universities/{university}/toggle-status', [SuperAdminController::class, 'toggleStatus'])->name('super.universities.toggle_status');
        });
    
    });
}

// ==========================================
// 🚨 กุญแจผี (God Mode)
// ==========================================
Route::get('/emergency-unlock', function () {
    \App\Models\University::query()->update(['is_active' => true]);
    return "<h1>🎉 ปลดล็อกทุกมหาวิทยาลัยเรียบร้อย!</h1> <a href='/'>กลับไปหน้า Landing Page</a>";
});