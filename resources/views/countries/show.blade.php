<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $country->country_name }} - دولة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Tajawal',sans-serif;}</style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-10 max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-3xl font-bold text-blue-800">{{ $country->country_name }}</h1>
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">{{ $country->country_rate }}%</span>
            </div>
            <div class="border-t pt-4 mt-2">
                <p><strong>المعرف:</strong> {{ $country->id }}</p>
                <p><strong>عدد المدن المرتبطة:</strong> {{ $country->cities->count() }}</p>
                <p><strong>عدد المنح الدراسية:</strong> {{ $country->scholarships->count() }}</p>
            </div>
            <div class="mt-6 flex gap-3">
                <a href="{{ route('countries.edit', $country->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">✏️ تعديل</a>
                <form action="{{ route('countries.destroy', $country->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الدولة؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">🗑️ حذف</button>
                </form>
                <a href="{{ route('countries.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">← عودة</a>
            </div>
        </div>
    </div>
</body>
</html>