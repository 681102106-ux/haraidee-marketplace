<!DOCTYPE html>
<html>
<head><title>ลงขายสินค้า</title></head>
<body style="padding: 50px; font-family: sans-serif;">
    <h2>ลงขายสินค้าใหม่ - {{ tenant('name') }}</h2>
    
    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div>
            <label>ชื่อสินค้า:</label><br>
            <input type="text" name="title" required value="{{ old('title') }}" style="width: 300px;">
        </div><br>
        
        <div>
            <label>หมวดหมู่:</label><br>
            <select name="category_id" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div><br>

        <div>
            <label>สภาพสินค้า:</label><br>
            <select name="condition_id" required>
                <option value="">-- เลือกสภาพสินค้า --</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                        {{ $condition->name }}
                    </option>
                @endforeach
            </select>
        </div><br>

        <div>
            <label>รายละเอียด:</label><br>
            <textarea name="description" required rows="4" style="width: 300px;">{{ old('description') }}</textarea>
        </div><br>

        <div>
            <label>ราคา (บาท):</label><br>
            <input type="number" name="price" required value="{{ old('price') }}" min="0">
        </div><br>

        <div>
            <label>อัปโหลดรูปภาพ (สูงสุด 5 รูป):</label><br>
            <input type="file" name="images[]" multiple accept="image/*" required>
        </div><br>
        
        <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
            ยืนยันการลงขาย
        </button>
        <a href="{{ route('products.index') }}" style="margin-left: 10px;">ยกเลิก</a>
    </form>
</body>
</html>