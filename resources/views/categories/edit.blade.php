<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل الفئة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 50px auto; background: white; border-radius: 12px; padding: 30px; }
        h1 { color: #1e3a8a; }
        label { font-weight: bold; margin-top: 15px; display: block; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 8px; }
        button { background: #1e3a8a; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-top: 25px; width: 100%; }
        .error { color: red; }
        .back-link { display: inline-block; margin-top: 20px; color: #1e3a8a; }
    </style>
</head>
<body>
<div class="container">
    <h1>✏️ تعديل الفئة</h1>
    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf @method('PUT')
        <div>
            <label>اسم الفئة <span style="color:red;">*</span></label>
            <input type="text" name="category_name" value="{{ old('category_name', $category->category_name) }}" required>
            @error('category_name') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button type="submit">💾 تحديث الفئة</button>
    </form>
    <a href="{{ route('categories.index') }}" class="back-link">← العودة إلى القائمة</a>
</div>
</body>
</html>