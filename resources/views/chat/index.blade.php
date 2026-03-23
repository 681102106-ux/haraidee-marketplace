<!DOCTYPE html>
<html>
<head><title>กล่องข้อความ (Inbox)</title></head>
<body style="padding: 20px; font-family: sans-serif; max-width: 800px; margin: auto;">

    <a href="{{ route('products.index') }}">⬅️ กลับตลาดนัด</a>
    
    <h2 style="color: {{ tenant('primary_color') ?? '#333' }};">💬 กล่องข้อความของฉัน</h2>

    @if($conversations->isEmpty())
        <div style="padding: 20px; background: #f9f9f9; text-align: center; border-radius: 8px; color: gray;">
            คุณยังไม่มีประวัติการพูดคุยกับใครเลย เริ่มไปช้อปปิ้งกันเถอะ!
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($conversations as $conv)
                @php
                    // เช็กว่าเรากำลังคุยกับใคร (ถ้าเราเป็นคนซื้อ อีกฝั่งคือคนขาย)
                    $amIBuyer = $conv->buyer_id === Auth::id();
                    $otherPerson = $amIBuyer ? $conv->seller : $conv->buyer;
                    $roleText = $amIBuyer ? 'ผู้ขาย' : 'ผู้ซื้อ';
                @endphp

                <a href="{{ route('chat.show', $conv->id) }}" style="text-decoration: none; color: black;">
                    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; background: white; transition: background 0.2s;">
                        
                        <div>
                            <h4 style="margin: 0; color: {{ tenant('primary_color') ?? '#007BFF' }};">
                                📦 {{ $conv->product->title }}
                            </h4>
                            <p style="margin: 5px 0 0 0; font-size: 0.9em; color: gray;">
                                คุยกับ: <b>{{ $otherPerson->name }}</b> ({{ $roleText }})
                            </p>
                        </div>

                        <div style="color: #007BFF; font-weight: bold;">
                            เข้าสู่แชท ➡️
                        </div>

                    </div>
                </a>
            @endforeach
        </div>
    @endif

</body>
</html>