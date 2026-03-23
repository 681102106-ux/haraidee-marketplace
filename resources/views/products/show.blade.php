<!DOCTYPE html>
<html>

<head>
    <title>{{ $product->title }}</title>
</head>

<body style="padding: 50px; font-family: sans-serif; max-width: 800px; margin: auto;">

    <a href="{{ route('products.index') }}">⬅️ กลับไปหน้าตลาดนัด</a>

    <h1 style="color: {{ tenant('primary_color') ?? '#333' }}; font-size: 2em;">{{ $product->title }}</h1>

    <div style="margin-bottom: 20px;">
        <span style="background: #eee; padding: 5px 10px; border-radius: 5px;">📂 {{ $product->category->name }}</span>
        <span style="background: #eee; padding: 5px 10px; border-radius: 5px;">✨ {{ $product->condition->name }}</span>
        <span style="color: green; font-weight: bold; font-size: 1.2em; margin-left: 15px;">💰 ฿{{
            number_format($product->price, 2) }}</span>
    </div>

    <div style="display: flex; gap: 10px; overflow-x: auto; margin-bottom: 30px;">
        @forelse($product->getMedia('product_images') as $image)
        <img src="{{ $image->getUrl() }}" alt="รูปสินค้า"
            style="height: 300px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
        @empty
        <div
            style="height: 300px; width: 300px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
            ไม่มีรูปภาพ
        </div>
        @endforelse
    </div>

    <div style="color: gray; font-size: 0.9em; margin-bottom: 10px;">
        👁️ มีผู้เข้าชมแล้ว: <b>{{ $product->views_count ?? 0 }}</b> ครั้ง
    </div>

    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3>รายละเอียดสินค้า:</h3>
        <p style="white-space: pre-wrap; line-height: 1.6;">{{ $product->description }}</p>
    </div>

    <div style="border-top: 2px solid #eee; padding-top: 20px;">
        <h3>ข้อมูลผู้ขาย:</h3>
        <p style="margin: 0; color: gray; font-size: 0.9em;">
            ผู้ขาย: <b>{{ $product->user->name }}</b>
            <span style="color: #ffc107;">⭐ {{ $product->user->average_rating }}</span>
            ({{ $product->user->receivedReviews->count() }} รีวิว)
        </p>
        @auth
        @if(Auth::id() !== $product->user_id)
        <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST" style="margin: 10px 0;">
            @csrf
            @php
            $isSaved = Auth::check() && Auth::user()->wishlists->contains($product->id);
            @endphp
            <button type="submit"
                style="background: white; border: 2px solid #dc3545; color: #dc3545; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.2s;">
                {{ $isSaved ? '💔 เอาออกจากรายการโปรด' : '❤️ บันทึกเก็บไว้ดู' }}
            </button>
        </form>
        <div style="margin-top: 20px;">
            @if($product->status == 'active')
            <form action="{{ route('chat.start', $product->id) }}" method="POST">
                @csrf
                <button type="submit"
                    style="width: 100%; background: white; color: {{ tenant('primary_color') ?? '#007BFF' }}; border: 2px solid {{ tenant('primary_color') ?? '#007BFF' }}; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1.1em;">
                    💬 ทักแชทเพื่อตกลงราคา
                </button>
            </form>

            @elseif($product->status == 'reserved')
            @php
            $isMyBill = \App\Models\Transaction::where('product_id', $product->id)
            ->where('payment_status', 'pending')
            ->where('user_id', Auth::id())
            ->exists();
            @endphp

            @if($isMyBill)
            <div
                style="background: #cce5ff; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #b8daff;">
                <h4 style="color: #004085; margin-top: 0;">🎉 ผู้ขายตกลงขายให้คุณแล้ว!</h4>
                <a href="{{ route('products.checkout', $product->id) }}" style="text-decoration: none;">
                    <div
                        style="background: #007BFF; color: white; padding: 12px; border-radius: 5px; font-weight: bold;">
                        💳 ดูบิลและสแกนชำระเงิน
                    </div>
                </a>
            </div>
            @else
            <button disabled
                style="width: 100%; background: #e9ecef; color: #6c757d; border: 1px solid #ced4da; padding: 10px; border-radius: 5px; font-weight: bold; cursor: not-allowed;">
                ⏳ สินค้าชิ้นนี้ติดจองแล้ว
            </button>
            @endif

            @elseif($product->status == 'sold')
            <button disabled
                style="width: 100%; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; font-weight: bold; cursor: not-allowed;">
                ❌ สินค้าถูกขายไปแล้ว
            </button>
            @endif
        </div>
        @else
        <span style="color: gray;">📌 นี่คือสินค้าของคุณเอง</span>
        @endif
        @else
        <a href="{{ route('login') }}" style="color: red;">กรุณาล็อกอินเพื่อทักแชท</a>
        @endauth
    </div>

</body>

</html>