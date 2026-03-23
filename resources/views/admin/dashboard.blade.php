<!DOCTYPE html>
<html>

<head>
    <title>Uni Admin Dashboard - {{ tenant('name') }}</title>
</head>

<body style="padding: 20px; font-family: sans-serif; background: #f4f6f9;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: {{ tenant('primary_color') ?? '#333' }}; margin: 0;">
            ⚙️ ศูนย์บัญชาการผู้ดูแล: {{ tenant('name') }}
        </h2>
        <a href="{{ route('products.index') }}"
            style="text-decoration: none; background: #6c757d; color: white; padding: 8px 15px; border-radius: 5px;">
            👀 ดูหน้าร้าน
        </a>
    </div>

    @if(session('success'))
    <div
        style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
        {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div
        style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
        {{ $errors->first() }}</div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px;">
        <div
            style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #007bff;">
            <h3 style="margin: 0; color: gray; font-size: 0.9em;">👥 นักศึกษาทั้งหมด</h3>
            <h1 style="margin: 10px 0 0 0; color: #333;">{{ number_format($stats['total_users']) }}</h1>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #17a2b8;">
            <h3 style="margin: 0; color: gray; font-size: 0.9em;">📦 สินค้าในระบบทั้งหมด</h3>
            <h1 style="margin: 10px 0 0 0; color: #333;">{{ number_format($stats['total_products']) }}</h1>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #ffc107;">
            <h3 style="margin: 0; color: gray; font-size: 0.9em;">🟢 สินค้าที่กำลังวางขาย</h3>
            <h1 style="margin: 10px 0 0 0; color: #333;">{{ number_format($stats['active_products']) }}</h1>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #28a745;">
            <h3 style="margin: 0; color: gray; font-size: 0.9em;">💸 ยอดซื้อขายหมุนเวียน</h3>
            <h1 style="margin: 10px 0 0 0; color: #28a745;">฿{{ number_format($stats['total_sales']) }}</h1>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">🏷️ จัดการหมวดหมู่สินค้า
            (Categories)</h3>

        <form action="{{ route('admin.categories.store') }}" method="POST"
            style="margin-bottom: 20px; display: flex; gap: 10px;">
            @csrf
            <input type="text" name="name" placeholder="ชื่อหมวดหมู่ใหม่..." required
                style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;">
            <button type="submit"
                style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                + เพิ่มหมวดหมู่
            </button>
        </form>


        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tr style="background: #f8f9fa;">
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">ID</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">ชื่อหมวดหมู่</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">จำนวนสินค้าที่ใช้</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">จัดการ</th>
            </tr>
            @foreach($categories as $cat)
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $cat->id }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><b>{{ $cat->name }}</b></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <span
                        style="background: #e8f4f8; color: #17a2b8; padding: 3px 8px; border-radius: 10px; font-size: 0.9em; font-weight: bold;">
                        กำลังขาย {{ $cat->active_count }} ชิ้น
                    </span>
                    <br>
                    <span style="color: gray; font-size: 0.8em;">(ประวัติรวม {{ $cat->products_count }} ชิ้น)</span>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    @if($cat->products_count == 0)
                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST"
                        onsubmit="return confirm('แน่ใจหรือไม่ที่จะลบหมวดหมู่นี้?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.8em;">🗑️
                            ลบ</button>
                    </form>
                    @else
                    <span style="color: gray; font-size: 0.8em;">ห้ามลบ (มีการใช้งาน)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>

    </div>
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">🏷️ จัดการสภาพสินค้า
            (Conditions)</h3>
        <form action="{{ route('admin.conditions.store') }}" method="POST"
            style="margin-bottom: 20px; display: flex; gap: 10px;">
            @csrf
            <input type="text" name="name" placeholder="ชื่อสภาพสินค้าใหม่..." required
                style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;">
            <button type="submit"
                style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                + เพิ่มสภาพสินค้า
            </button>
        </form>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <tr style="background: #f8f9fa;">
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">ID</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">ชื่อสภาพสินค้า</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">จำนวนสินค้าที่ใช้</th>
                <th style="padding: 12px; border-bottom: 2px solid #ddd;">จัดการ</th>
            </tr>
            @foreach($conditions as $cond)
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">{{ $cond->id }}</td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;"><b>{{ $cond->name }}</b></td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    <span
                        style="background: #e8f4f8; color: #17a2b8; padding: 3px 8px; border-radius: 10px; font-size: 0.9em; font-weight: bold;">
                        กำลังขาย {{ $cond->active_count }} ชิ้น
                    </span>
                    <br>
                    <span style="color: gray; font-size: 0.8em;">(ประวัติรวม {{ $cond->products_count }} ชิ้น)</span>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #eee;">
                    @if($cond->products_count == 0)
                    <form action="{{ route('admin.conditions.destroy', $cond->id) }}" method="POST"
                        onsubmit="return confirm('แน่ใจหรือไม่ที่จะลบสภาพสินค้านี้?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.8em;">🗑️
                            ลบ</button>
                    </form>
                    @else
                    <span style="color: gray; font-size: 0.8em;">ห้ามลบ (มีการใช้งาน)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>

        <div
            style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-top: 30px;">
            <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; color: #dc3545;">
                🛑 ตรวจสอบสินค้าล่าสุด (Moderation)
            </h3>

            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9em;">
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">รูปภาพ</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">ชื่อสินค้า</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">ผู้ขาย</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">ราคา</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">สถานะ</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">จัดการ (แบน)</th>
                </tr>
                @foreach($recent_products as $prod)
                <tr style="background: {{ $prod->status == 'banned' ? '#fff3cd' : 'white' }};">
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <img src="{{ $prod->getFirstMediaUrl('product_images') ?: 'https://via.placeholder.com/50' }}"
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="{{ route('products.show', $prod->id) }}" target="_blank"
                            style="text-decoration: none; color: #007bff; font-weight: bold;">
                            {{ Str::limit($prod->title, 30) }}
                        </a>
                        <br><span style="color: gray; font-size: 0.8em;">{{ $prod->category->name ?? '-' }}</span>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $prod->user->name }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #28a745;">฿{{
                        number_format($prod->price, 2) }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        @if($prod->status == 'active') <span style="color: #28a745;">🟢 ปกติ</span>
                        @elseif($prod->status == 'reserved') <span style="color: #ffc107;">⏳ ติดจอง</span>
                        @elseif($prod->status == 'sold') <span style="color: gray;">✔️ ขายแล้ว</span>
                        @elseif($prod->status == 'banned') <span style="color: red; font-weight: bold;">🔴
                            ถูกระงับ</span>
                        @endif
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <form action="{{ route('admin.products.toggle_status', $prod->id) }}" method="POST"
                            onsubmit="return confirm('ยืนยันการเปลี่ยนสถานะสินค้านี้?');">
                            @csrf
                            @method('PATCH')
                            @if($prod->status == 'banned')
                            <button type="submit"
                                style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.9em;">
                                🟢 ปลดแบน
                            </button>
                            @else
                            <button type="submit"
                                style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.9em;">
                                🔴 ระงับการขาย
                            </button>
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>


</body>

</html>