<!DOCTYPE html>
<html>

<body style="padding: 20px; font-family: sans-serif;">
    <a href="{{ route('products.index') }}">⬅️ กลับตลาดนัด</a>
    <h2>📦 สินค้าของฉัน ({{ tenant('name') }})</h2>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left;">
        <tr style="background: #eee;">
            <th>ชื่อสินค้า</th>
            <th>ราคา</th>
            <th>สถานะ</th>
            <th>จัดการ</th>
        </tr>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->title }}</td>
            <td>฿{{ number_format($product->price, 2) }}</td>
            <td>
                @if($product->status == 'active') 🟢 กำลังขาย
                @elseif($product->status == 'sold') 🔴 ขายแล้ว
                @else ❌ โดนแบน @endif
            </td>
            <td>
                @if($product->status == 'active')
                @if($product->conversations->count() > 0)
                <form action="{{ route('products.offers.send', $product->id) }}" method="POST"
                    style="display: flex; gap: 5px; flex-direction: column;">
                    @csrf
                    <select name="buyer_id" required
                        style="padding: 5px; border: 1px solid #113264; border-radius: 3px;">
                        <option value="">-- เลือกคนที่จะส่งบิลให้ --</option>
                        @foreach($product->conversations as $conv)
                        <option value="{{ $conv->buyer_id }}">{{ $conv->buyer->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        style="color: white; background: #113264; padding: 5px; border: none; cursor:pointer; border-radius: 3px;">
                        🧾 ส่งบิลเรียกเก็บเงิน
                    </button>
                </form>
                @else
                <span style="color: gray; font-size: 0.9em;">รอคนทักแชทเพื่อส่งบิล...</span>
                @endif

                @elseif($product->status == 'reserved')
                <div
                    style="background: #fff3cd; padding: 10px; border-radius: 5px; text-align: center; border: 1px solid #ffeeba;">
                    <span style="color: #856404; font-weight: bold; font-size: 0.9em;">⏳ รอผู้ซื้อโอนเงิน</span>
                    <div style="display: flex; gap: 5px; margin-top: 10px; justify-content: center;">
                        <form action="{{ route('products.sold', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.8em;">✅
                                ได้รับเงินแล้ว</button>
                        </form>
                        <form action="{{ route('products.offers.cancel', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.8em;">🔓
                                ยกเลิกบิล</button>
                        </form>
                    </div>
                </div>

                @elseif($product->status == 'sold')
                <span style="color: #28a745; font-weight: bold;">✔️ ขายเรียบร้อย</span>
                @endif

                <hr style="margin: 10px 0; border: 0; border-top: 1px dashed #ccc;">
                <div style="display: flex; gap: 5px;">
                    <a href="{{ route('products.edit', $product->id) }}"
                        style="background: #ffc107; color: black; text-decoration: none; padding: 5px 10px; border-radius: 3px; font-size: 0.9em;">
                        ✏️ แก้ไข
                    </a>

                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                        onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; font-size: 0.9em; cursor: pointer;">
                            🗑️ ลบ
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </table>
</body>

</html>