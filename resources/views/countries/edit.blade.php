<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل دولة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Tajawal',sans-serif;}</style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-10 max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h1 class="text-2xl font-bold mb-6">✏️ تعديل الدولة: {{ $country->country_name }}</h1>
            <form action="{{ route('countries.update', $country->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label>اسم الدولة</label>
                    <input type="text" name="country_name" value="{{ old('country_name', $country->country_name) }}" required
                           class="w-full border rounded-lg p-2">
                </div>
                <div class="mb-6">
                    <label>نسبة التقييم (%)</label>
                    <input type="number" step="0.01" name="country_rate" value="{{ old('country_rate', $country->country_rate) }}" required
                           class="w-full border rounded-lg p-2">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">💾 تحديث</button>
                <a href="{{ route('countries.index') }}" class="text-gray-500 mr-4">إلغاء</a>
            </form>
        </div>
    </div>
</body>
</html>