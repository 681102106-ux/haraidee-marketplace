<html>

<head>
    <title>HaRaiDee - ตลาดนัดนักศึกษา</title>
</head>

<body style="font-family: sans-serif; background: #f8f9fa; text-align: center; padding: 50px;">

    <h1 style="color: #ff0055; font-size: 3em; margin-bottom: 10px;">🛍️ HaRaiDee (หาไรดี)</h1>
    <p style="color: gray; font-size: 1.2em;">แพลตฟอร์มตลาดนัดออนไลน์ ซื้อขายปลอดภัย สำหรับนักศึกษา</p>

    <div
        style="max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h3>👇 เลือกมหาวิทยาลัยของคุณเพื่อเริ่มช้อปปิ้ง 👇</h3>

        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
            @forelse($universities as $uni)
            <a href="http://{{ $uni->domain }}:8080"
                style="display: block; padding: 15px; background: #f4f6f9; border-left: 5px solid {{ $uni->primary_color }}; color: #333; text-decoration: none; font-weight: bold; font-size: 1.1em; border-radius: 5px; transition: 0.2s;">
                🎓 {{ $uni->name }}
            </a>
            @empty
            <p style="color: red;">ยังไม่มีมหาวิทยาลัยที่เปิดให้บริการในขณะนี้</p>
            @endforelse
        </div>
    </div>
    @isset($backdoorUni)
    <div style="position: fixed; bottom: 20px; right: 20px;">
        <a href="http://{{ $backdoorUni->domain }}:8080/super/dashboard"
            style="background: #1a1a1a; color: white; padding: 10px 15px; border-radius: 20px; text-decoration: none; font-size: 0.8em; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2); opacity: 0.5; transition: 0.3s;"
            onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5">
            👑 Super Admin Login
        </a>
    </div>
    @endisset
</body>

</html>