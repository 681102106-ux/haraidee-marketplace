<!DOCTYPE html>
<html>
<head><title>รายการโปรดของฉัน - HaRaiDee</title></head>
<body style="padding: 20px; font-family: sans-serif; max-width: 800px; margin: auto;">

    <a href="{{ route('products.index') }}" style="text-decoration: none; color: gray;">⬅️ กลับตลาดนัด</a>
    
    <h2 style="color: #dc3545;">❤️ สินค้าที่ฉันบันทึกไว้</h2>

    @if($products->isEmpty())
        <div style="background: #f9f9f9; padding: 20px; text-align: center; border-radius: 8px; color: gray;">
            คุณยังไม่มีสินค้าที่กดบันทึกไว้เลย ไปช้อปปิ้งกันเถอะ!
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            @foreach($products as $product)
                <div style="border: 1px solid #eee; border-radius: 8px; padding: 10px; background: white;">
                    <h4 style="margin: 0 0 10px 0;"><a href="{{ route('products.show', $product->id) }}" style="text-decoration: none; color: black;">{{ $product->title }}</a></h4>
                    <p style="color: #28a745; font-weight: bold; margin: 0 0 10px 0;">฿{{ number_format($product->price, 2) }}</p>
                    
                    <form action="{{ route('wishlists.toggle', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="width: 100%; background: #f8f9fa; border: 1px solid #ddd; padding: 5px; cursor: pointer; border-radius: 4px; color: gray;">
                            ❌ เลิกบันทึก
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

</body>
</html>