@extends('layouts.tenant')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-end border-b-2 border-tenant-primary/20 pb-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">
                ตลาดนัด <span class="text-tenant-primary">{{ tenant('name') }}</span>
            </h2>
            <p class="text-gray-500 mt-1">สินค้ามือสอง อัปเดตล่าสุดจากเพื่อนร่วมสถาบัน</p>
        </div>

        <a href="{{ route('products.create') }}"
            class="bg-tenant-primary text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-tenant-primary">
            + ลงขายสินค้า
        </a>
    </div>

    <div class="mb-8 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">

            <div class="grow">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาสินค้าที่ต้องการ..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-tenant-primary focus:ring focus:ring-tenant-primary/20 p-2.5 border">
            </div>

            <div class="md:w-64">
                <select name="category"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-tenant-primary focus:ring focus:ring-tenant-primary/20 p-2.5 border bg-white">
                    <option value="">ทุกหมวดหมู่</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-tenant-primary text-white px-6 py-2.5 rounded-lg hover:opacity-90 transition font-medium">
                    🔍 ค้นหา
                </button>
                @if(request()->has('search') || request()->has('category'))
                <a href="{{ route('products.index') }}"
                    class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium flex items-center justify-center">
                    ล้างค่า
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6" x-data="{ hoveredId: null }">

        @forelse($products as $product)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 relative"
            @mouseenter="hoveredId = {{ $product->id }}" @mouseleave="hoveredId = null">

            @if($product->is_boosted)
            <div
                style="background: #ffc107; color: #000; padding: 3px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; display: inline-block; margin-bottom: 5px;">
                🔥 สินค้าแนะนำ
            </div>
            @endif

            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                @if($product->hasMedia('product_images'))
                <img src="{{ $product->getFirstMediaUrl('product_images', 'optimized') }}" alt="{{ $product->title }}"
                    class="w-full h-full object-cover transform transition duration-500"
                    :class="hoveredId === {{ $product->id }} ? 'scale-110' : 'scale-100'">
                @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                @endif

                <span
                    class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm text-xs font-semibold px-2 py-1 rounded-md text-gray-700 shadow-sm">
                    {{ $product->condition->name ?? 'ทั่วไป' }}
                </span>
            </div>

            <div class="p-4">
                <div class="text-xs text-tenant-primary font-semibold mb-1">
                    {{ $product->category->name ?? 'ไม่ระบุหมวดหมู่' }}
                </div>
                <a href="{{ route('products.show', $product) }}" class="block">
                    <h3 class="font-bold text-gray-800 text-sm truncate" title="{{ $product->title }}">
                        {{ $product->title }}
                    </h3>
                </a>
                <div class="mt-2 flex justify-between items-center">
                    <span class="text-lg font-extrabold text-gray-900">
                        ฿{{ number_format($product->price, 0) }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ $product->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                </path>
            </svg>
            <p>ยังไม่มีสินค้าในหมวดหมู่นี้ เริ่มลงขายคนแรกเลย!</p>
        </div>
        @endforelse

    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>

</div>
@endsection