<!DOCTYPE html>
<html>
<head><title>แก้ไขสินค้า - HaRaiDee</title></head>
<body style="padding: 20px; font-family: sans-serif; max-width: 600px; margin: auto;">

    <a href="{{ route('products.my_products') }}" style="text-decoration: none; color: gray;">⬅️ กลับหน้าสินค้าของฉัน</a>
    
    <h2 style="color: {{ tenant('primary_color') ?? '#333' }};">✏️ แก้ไขสินค้า: {{ $product->title }}</h2>

    @if($errors->any())
        <div style="background: #dc3545; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px;">{{ $errors->first() }}</div>
    @endif

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
            @csrf
            @method('PUT')

            <div>
                <label>ชื่อสินค้า: <span style="color: red;">*</span></label>
                <input type="text" name="title" value="{{ $product->title }}" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>หมวดหมู่: <span style="color: red;">*</span></label>
                    <select name="category_id" required style="width: 100%; padding: 10px; margin-top: 5px;">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>สภาพสินค้า: <span style="color: red;">*</span></label>
                    <select name="condition_id" required style="width: 100%; padding: 10px; margin-top: 5px;">
                        @foreach($conditions as $cond)
                            <option value="{{ $cond->id }}" {{ $product->condition_id == $cond->id ? 'selected' : '' }}>
                                {{ $cond->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label>ราคา (บาท): <span style="color: red;">*</span></label>
                <input type="number" name="price" value="{{ $product->price }}" min="0" required style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>

            <div>
                <label>รายละเอียดสินค้า: <span style="color: red;">*</span></label>
                <textarea name="description" rows="5" required style="width: 100%; padding: 10px; margin-top: 5px;">{{ $product->description }}</textarea>
            </div>

            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                <label style="font-weight: bold;">รูปภาพสินค้า (อัปโหลดใหม่จะแทนที่รูปเก่าทั้งหมด):</label>
                <input type="file" name="images[]" multiple accept="image/*" style="width: 100%; margin-top: 10px;">
                <p style="font-size: 0.8em; color: gray; margin-top: 5px;">* ปล่อยว่างไว้หากต้องการใช้รูปภาพเดิม</p>
                
                <div style="display: flex; gap: 5px; margin-top: 10px; flex-wrap: wrap;">
                    @foreach($product->getMedia('product_images') as $media)
                        <img src="{{ $media->getUrl() }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd;">
                    @endforeach
                </div>
            </div>

            <button type="submit" style="background: {{ tenant('primary_color') ?? '#007BFF' }}; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1em; margin-top: 10px;">
                💾 อัปเดตสินค้า
            </button>
        </form>

    </div>
</body>
</html>