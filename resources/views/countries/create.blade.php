<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة دولة جديدة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Tajawal',sans-serif;}</style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-10 max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-r-4 border-blue-600 pr-3">➕ إضافة دولة جديدة</h1>
            <form action="{{ route('countries.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">اسم الدولة <span class="text-red-500">*</span></label>
                    <input type="text" name="country_name" value="{{ old('country_name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('country_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">نسبة التقييم <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="country_rate" value="{{ old('country_rate') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('country_rate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-between items-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow transition">💾 حفظ الدولة</button>
                    <a href="{{ route('countries.index') }}" class="text-gray-500 hover:text-gray-700">← إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>