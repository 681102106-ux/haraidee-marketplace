@extends('layouts.tenant')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">เข้าสู่ระบบ</h2>
            <p class="text-gray-500 mt-2">ตลาดนัด {{ tenant('name') }}</p>
        </div>

        <form action="{{ url()->current() }}" method="POST">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">อีเมลนักศึกษา</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-tenant-primary focus:ring focus:ring-tenant-primary/20 p-2.5 border">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-tenant-primary focus:ring focus:ring-tenant-primary/20 p-2.5 border">
            </div>

            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 text-tenant-primary border-gray-300 rounded focus:ring-tenant-primary">
                <label for="remember" class="ml-2 block text-sm text-gray-900">จดจำการเข้าสู่ระบบ</label>
            </div>

            <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-tenant-primary hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-tenant-primary transition-all">
                เข้าสู่ระบบ
            </button>
        </form>
    </div>
</div>
@endsection