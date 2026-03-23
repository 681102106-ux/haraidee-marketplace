<!DOCTYPE html>
<html>
<head><title>สมัครสมาชิก</title></head>
<body style="padding: 50px; font-family: sans-serif;">
    <h2>สมัครสมาชิกตลาดนัด: {{ tenant('name') }}</h2>
    
    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <label>ชื่อ-นามสกุล:</label><br>
            <input type="text" name="name" required value="{{ old('name') }}">
        </div><br>
        
        <div>
            <label>อีเมล:</label><br>
            <input type="email" name="email" required value="{{ old('email') }}">
        </div><br>
        
        <div>
            <label>รหัสผ่าน:</label><br>
            <input type="password" name="password" required>
        </div><br>
        
        <div>
            <label>ยืนยันรหัสผ่าน:</label><br>
            <input type="password" name="password_confirmation" required>
        </div><br>
        
        <button type="submit" style="padding: 10px 20px;">สมัครสมาชิก</button>
    </form>
</body>
</html>