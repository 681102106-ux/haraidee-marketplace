<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantActive
{
   public function handle(Request $request, Closure $next): Response
    {
        if (tenant() && !tenant('is_active')) {
            
            if ($request->is('login') || $request->is('logout') || $request->is('super/*')) {
                return $next($request);
            }

            abort(403, '⛔ แพลตฟอร์มของมหาวิทยาลัยนี้ถูกระงับการใช้งานชั่วคราว');
        }

        return $next($request);
    }
}