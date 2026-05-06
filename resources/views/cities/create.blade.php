<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مدينة جديدة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 50px auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; margin-bottom: 20px; border-right: 4px solid #1e3a8a; padding-right: 15px; }
        label { font-weight: bold; margin-top: 15px; display: block; color: #333; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; }
        button { background: #1e3a8a; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-top: 25px; width: 100%; font-size: 16px; }
        button:hover { background: #2563eb; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .back-link { display: inline-block; margin-top: 20px; color: #1e3a8a; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <h1>➕ إضافة مدينة جديدة</h1>

    <form action="{{ route('cities.store') }}" method="POST">
        @csrf

        <div>
            <label>اسم المدينة <span style="color: red;">*</span></label>
            <input type="text" name="city_name" value="{{ old('city_name') }}" required placeholder="مثال: دمشق">
            @error('city_name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label>الدولة <span style="color: red;">*</span></label>
            <select name="country_id" required>
                <option value="">-- اختر الدولة --</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->country_name }}
                    </option>
                @endforeach
            </select>
            @error('country_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">💾 حفظ المدينة</button>
    </form>

    <a href="{{ route('cities.index') }}" class="back-link">← العودة إلى قائمة المدن</a>
</div>
</body>
</html>