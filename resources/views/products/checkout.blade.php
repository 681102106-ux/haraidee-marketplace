<!DOCTYPE html>
<html>
<head><title>ชำระเงิน - HaRaiDee</title></head>
<body style="padding: 20px; font-family: sans-serif; background: #f4f7f6; max-width: 500px; margin: auto;">

    <a href="{{ route('products.show', $product->id) }}" style="text-decoration: none; color: gray;">⬅️ กลับไปหน้าสินค้า</a>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; margin-top: 20px;">
        
        <h2 style="color: {{ tenant('primary_color') ?? '#007BFF' }}; margin-top: 0;">🛒 สรุปการสั่งซื้อ</h2>
        
        <div style="text-align: left; background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin: 0 0 5px 0;">📦 {{ $product->title }}</h4>
            <p style="margin: 0; color: gray; font-size: 0.9em;">ผู้ขาย: {{ $seller->name }}</p>
            <h3 style="margin: 10px 0 0 0; color: #28a745;">ยอดชำระ: ฿{{ number_format($product->price, 2) }}</h3>
        </div>

        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">

        <h3 style="color: #113264;">สแกนจ่ายด้วย PromptPay</h3>

        @if(empty($seller->phone))
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px;">
                ⚠️ ผู้ขายยังไม่ได้ตั้งค่าเบอร์ PromptPay<br>
                <form action="{{ route('chat.start', $product->id) }}" method="POST" style="margin-top: 10px;">
                    @csrf
                    <button type="submit" style="background: #ffc107; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        💬 ทักแชทเพื่อขอเลขบัญชี
                    </button>
                </form>
            </div>
        @else
            @php
                // รูปแบบ API: https://promptpay.io/{เบอร์โทร}/{จำนวนเงิน}.png
                $promptPayUrl = "https://promptpay.io/{$seller->phone}/{$product->price}.png";
            @endphp
            
            <div style="border: 2px solid #113264; display: inline-block; padding: 15px; border-radius: 10px; background: white;">
                <img src="{{ $promptPayUrl }}" alt="PromptPay QR Code" style="width: 200px; height: 200px;">
            </div>
            
            <p style="font-size: 0.9em; color: gray; margin-top: 15px;">
                ชื่อบัญชีพร้อมเพย์ผูกกับเบอร์: <b>{{ $seller->phone }}</b><br>
                สแกนผ่านแอปพลิเคชันธนาคารได้ทุกธนาคาร
            </p>

            <form action="{{ route('chat.start', $product->id) }}" method="POST" style="margin-top: 20px;">
                @csrf
                <button type="submit" style="background: {{ tenant('primary_color') ?? '#007BFF' }}; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; font-size: 1.1em;">
                    📸 โอนแล้ว? คลิกส่งสลิปในแชทเลย!
                </button>
            </form>
        @endif

    </div>
</body>
</html>