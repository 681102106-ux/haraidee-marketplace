<!DOCTYPE html>
<html>
<head><title>Super Admin Dashboard - HaRaiDee</title></head>
<body style="padding: 20px; font-family: sans-serif; background-color: #f0f2f5;">

    <div style="display: flex; justify-content: space-between; align-items: center; background: #1a1a1a; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin: 0;">👑 HaRaiDee - Central Management</h2>
        <form method="POST" action="{{ route('logout') }}">
            @csrf <button style="padding: 8px 15px; cursor: pointer; background: #dc3545; color: white; border: none; border-radius: 4px; font-weight: bold;">ออกจากระบบ</button>
        </form>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ $errors->first() }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #007bff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="margin: 0; color: gray; font-size: 0.9em; font-weight: bold;">👥 จำนวนผู้ใช้ทั้งหมด (ทั่วประเทศ)</p>
            <h1 style="margin: 10px 0 0 0; color: #333;">{{ number_format($globalStats['total_users']) }} <span style="font-size: 0.5em; color: gray;">คน</span></h1>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #17a2b8; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="margin: 0; color: gray; font-size: 0.9em; font-weight: bold;">📦 สินค้าในระบบทั้งหมด (ทั่วประเทศ)</p>
            <h1 style="margin: 10px 0 0 0; color: #333;">{{ number_format($globalStats['total_products']) }} <span style="font-size: 0.5em; color: gray;">ชิ้น</span></h1>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #28a745; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <p style="margin: 0; color: gray; font-size: 0.9em; font-weight: bold;">💸 ยอดเงินหมุนเวียนรวม (GMV)</p>
            <h1 style="margin: 10px 0 0 0; color: #28a745;">฿{{ number_format($globalStats['total_sales'], 2) }}</h1>
        </div>
    </div>

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        
        <div style="background: white; padding: 20px; border-radius: 8px; flex: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0;">➕ เพิ่มมหาวิทยาลัยใหม่ (New Tenant)</h3>
            <form action="{{ route('super.universities.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9em; font-weight: bold;">ชื่อมหาวิทยาลัย:</label><br>
                    <input type="text" name="name" required placeholder="เช่น มหาวิทยาลัยธรรมศาสตร์" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9em; font-weight: bold;">Subdomain (Domain หลัก):</label><br>
                    <input type="text" name="domain" required placeholder="เช่น tu.localhost" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.9em; font-weight: bold;">สีประจำมหาวิทยาลัย (HEX):</label><br>
                    <input type="color" name="primary_color" value="#cc0000" style="width: 100%; height: 40px; margin-top: 5px; cursor: pointer; border: none;">
                </div>
                <button type="submit" style="width: 100%; padding: 12px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1em;">
                    🚀 สร้างระบบให้มหาลัยนี้ทันที!
                </button>
            </form>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; flex: 2; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
            <h3 style="margin-top: 0;">🏛️ มหาวิทยาลัยในระบบทั้งหมด ({{ $universities->count() }} แห่ง)</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9em;">
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">ข้อมูลมหาลัย</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">สถานะ</th>
                    <th style="padding: 12px; border-bottom: 2px solid #ddd;">จัดการแอดมิน (Uni Admin)</th>
                </tr>
                @foreach($universities as $uni)
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">
                        <b style="font-size: 1.1em; color: #333;">{{ $uni->name }}</b><br>
                        <code style="background: #e9ecef; padding: 2px 5px; border-radius: 3px; color: #d63384;">{{ $uni->domain }}</code><br>
                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px; font-size: 0.8em; color: gray;">
                            สี Theme: <div style="width: 12px; height: 12px; background: {{ $uni->primary_color }}; border-radius: 50%;"></div> {{ $uni->primary_color }}
                        </div>
                    </td>
                    
                    <td style="padding: 12px; border-bottom: 1px solid #eee;">
                        <form action="{{ route('super.universities.toggle_status', $uni->id) }}" method="POST" onsubmit="return confirm('ยืนยันการเปลี่ยนสถานะมหาวิทยาลัยนี้?');">
                            @csrf
                            @method('PATCH')
                            @if($uni->is_active)
                                <button type="submit" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 5px 10px; border-radius: 15px; cursor: pointer; font-weight: bold;">
                                    🟢 Active (กดเพื่อปิด)
                                </button>
                            @else
                                <button type="submit" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 5px 10px; border-radius: 15px; cursor: pointer; font-weight: bold;">
                                    🔴 Inactive (กดเพื่อเปิด)
                                </button>
                            @endif
                        </form>
                    </td>
                    
                    <td style="padding: 12px; border-bottom: 1px solid #eee; min-width: 200px;">
                        <a href="http://{{ $uni->domain }}:8080" target="_blank" style="display: inline-block; background: #f8f9fa; color: #0d6efd; text-decoration: none; font-weight: bold; padding: 5px 10px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 10px;">
                            🌐 เปิดหน้าร้าน ↗️
                        </a>
                        
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px dashed #ccc;">
                            <div style="font-size: 0.8em; color: gray; margin-bottom: 5px; font-weight: bold;">+ สร้างรหัสผ่านให้ Uni Admin:</div>
                            <form action="{{ route('super.universities.admins.store', $uni->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 5px;">
                                @csrf
                                <input type="text" name="name" placeholder="ชื่อ Admin" required style="padding: 6px; border: 1px solid #ccc; border-radius: 3px; font-size: 0.9em;">
                                <input type="email" name="email" placeholder="อีเมล" required style="padding: 6px; border: 1px solid #ccc; border-radius: 3px; font-size: 0.9em;">
                                <input type="password" name="password" placeholder="รหัสผ่าน" required style="padding: 6px; border: 1px solid #ccc; border-radius: 3px; font-size: 0.9em;">
                                <button type="submit" style="background: #28a745; color: white; border: none; padding: 6px; cursor: pointer; border-radius: 3px; font-weight: bold; margin-top: 5px;">
                                    เพิ่ม Uni Admin
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

</body>
</html>