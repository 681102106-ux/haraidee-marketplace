<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
   // ==========================================
    // 1. โชว์หน้าฟอร์มแก้ไขโปรไฟล์
    // ==========================================
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // ==========================================
    // 2. บันทึกข้อมูลโปรไฟล์ และเบอร์พร้อมเพย์
    // ==========================================
    public function update(Request $request)
    {
       $user = \App\Models\User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15', 
            'password' => 'nullable|string|min:6|confirmed', 
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;


        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar'); 
            $user->addMedia($request->file('avatar'))->toMediaCollection('avatar'); 
        }

        return back()->with('success', '✅ อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว!');
    }
}