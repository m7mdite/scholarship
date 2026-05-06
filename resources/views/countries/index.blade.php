<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدول | منصة المنح</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .card-hover:hover { transform: translateY(-3px); transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- الهيدر -->
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-blue-800 border-r-4 border-blue-600 pr-4">
                🌍 الدول
            </h1>
            <a href="{{ route('countries.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة دولة جديدة
            </a>
        </div>

        <!-- رسالة نجاح -->
        @if(session('success'))
            <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- شبكة البطاقات -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($countries as $country)
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover transition border border-gray-100">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-2">
                                <span class="text-3xl">🌍</span>
                                <h2 class="text-xl font-bold text-gray-800">{{ $country->country_name }}</h2>
                            </div>
                            <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-2 py-1 rounded-full">{{ $country->country_rate }}%</span>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <a href="{{ route('countries.show', $country->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center gap-1">
                                عرض التفاصيل <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <div class="flex gap-2">
                                <a href="{{ route('countries.edit', $country->id) }}" class="text-gray-500 hover:text-yellow-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('countries.destroy', $country->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الدولة؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-lg shadow">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-700">لا توجد دول مضافة بعد</h3>
                    <p class="text-gray-500 mt-1">أضف أول دولة الآن عبر زر "إضافة دولة جديدة".</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>