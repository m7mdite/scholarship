<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة تخصص جديد</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 50px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; }
        label { font-weight: bold; margin-top: 15px; display: block; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 8px; }
        button { background: #1e3a8a; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-top: 25px; width: 100%; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .back-link { display: inline-block; margin-top: 20px; color: #1e3a8a; }
    </style>
</head>
<body>
<div class="container">
    <h1>➕ إضافة تخصص جديد</h1>
    <form action="{{ route('specializations.store') }}" method="POST">
        @csrf
        <div>
            <label>اسم التخصص <span style="color:red;">*</span></label>
            <input type="text" name="specialization_name" value="{{ old('specialization_name') }}" required>
            @error('specialization_name') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label>الفئة <span style="color:red;">*</span></label>
            <select name="category_id" required>
                <option value="">-- اختر الفئة --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button type="submit">💾 حفظ التخصص</button>
    </form>
    <a href="{{ route('specializations.index') }}" class="back-link">← العودة إلى القائمة</a>
</div>
</body>
</html>