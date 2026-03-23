<!DOCTYPE html>
<html>

<head>
    <title>ตั้งค่าโปรไฟล์ - {{ tenant('name') }}</title>
</head>

<body style="font-family: sans-serif; background: #f4f6f9; padding: 20px;">

    <div
        style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <div
            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h2 style="margin: 0; color: {{ tenant('primary_color') ?? '#333' }};">⚙️ ตั้งค่าบัญชีผู้ใช้</h2>
            <a href="{{ route('products.index') }}" style="text-decoration: none; color: gray;">⬅️ กลับหน้าร้าน</a>
        </div>

        @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">{{
            session('success') }}</div>
        @endif
        @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">{{
            $errors->first() }}</div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="text-align: center; margin-bottom: 20px;">
                <img src="{{ Auth::user()->getFirstMediaUrl('avatar') ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=random' }}"
                    style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid {{ tenant('primary_color') ?? '#ccc' }}; margin-bottom: 10px;">
                <br>
                <input type="file" name="avatar" accept="image/*" style="font-size: 0.9em;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; font-size: 0.9em; color: gray;">ชื่อ-นามสกุล:</label><br>
                <input type="text" name="name" value="{{ Auth::user()->name }}" required
                    style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div
                style="margin-bottom: 15px; background: #e8f4f8; padding: 15px; border-radius: 8px; border: 1px solid #b8daff;">
                <label style="font-weight: bold; font-size: 0.9em; color: #0056b3;">📲 เบอร์ PromptPay
                    (สำหรับรับเงิน):</label>
                <p style="margin: 5px 0; font-size: 0.8em; color: #666;">*กรอกเบอร์มือถือหรือเลขบัตรประชาชน 10-13 หลัก
                    (ใช้สร้าง QR Code ส่งในแชท)</p>
                <input type="text" name="phone" value="{{ Auth::user()->phone }}" placeholder="เช่น 0812345678"
                    style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 1.1em; letter-spacing: 1px;">
            </div>

            <div style="margin-bottom: 15px; border-top: 1px dashed #ccc; padding-top: 15px;">
                <label style="font-weight: bold; font-size: 0.9em; color: gray;">เปลี่ยนรหัสผ่าน
                    (ปล่อยว่างได้ถ้าไม่ต้องการเปลี่ยน):</label><br>
                <input type="password" name="password" placeholder="รหัสผ่านใหม่"
                    style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-bottom: 10px;">
                <input type="password" name="password_confirmation" placeholder="ยืนยันรหัสผ่านใหม่"
                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit"
                style="width: 100%; padding: 12px; background: {{ tenant('primary_color') ?? '#007BFF' }}; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1em; margin-top: 10px;">
                💾 บันทึกการตั้งค่า
            </button>
        </form>

    </div>

</body>

</html>