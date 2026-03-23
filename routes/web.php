<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Models\University;

$centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);

foreach ($centralDomains as $domain) {
    Route::domain($domain)->group(function () {

        Route::get('/', function () {

            $universities = University::where('is_active', true)->get();

            $backdoorUni = University::first();

            return view('welcome', compact('universities', 'backdoorUni'));
        });
    });
    }
    
    // ==========================================
    // 🚨 กุญแจผี (God Mode) สำหรับปลดล็อกทุกมหาลัย
    // ==========================================
    Route::get('/emergency-unlock', function () {
        \App\Models\University::query()->update(['is_active' => true]);
        return "<h1>🎉 ปลดล็อกทุกมหาวิทยาลัยเรียบร้อย!</h1> <a href='/'>กลับไปหน้า Landing Page</a>";
    });
    