<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة المنح الدراسية</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- تخصيص بسيط للخط -->
    <style>
        body {
            font-family: 'Tajawal', system-ui, sans-serif;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            transition: all 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-8">
        <!-- الهيدر -->
        <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-blue-800 border-r-4 border-blue-600 pr-4">
                🎓 المنح الدراسية
            </h1>
            <a href="{{ route('scholarships.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                إضافة منحة جديدة
            </a>
        </div>

        <!-- رسالة نجاح (إن وجدت) -->
        @if(session('success'))
            <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- شبكة البطاقات -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($scholarships as $scholarship)
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover transition-all duration-300 border border-gray-100">
                    <div class="p-5">
                        <!-- عنوان المنحة -->
                        <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1">
                            {{ $scholarship->scholarship_name }}
                        </h2>
                        
                        <!-- تفاصيل مختصرة -->
                        <div class="space-y-2 text-sm text-gray-600 mt-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $scholarship->country->country_name ?? 'غير محدد' }}</span>
                                @if($scholarship->city)
                                    <span class="text-gray-400">•</span>
                                    <span>{{ $scholarship->city->city_name }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>الدرجة: {{ $scholarship->degree ?? '-' }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2"></path></svg>
                                <span>التمويل: {{ $scholarship->finance ?? '-' }}</span>
                            </div>

                            @if($scholarship->start_date)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>يبدأ: {{ \Carbon\Carbon::parse($scholarship->start_date)->format('Y-m-d') }}</span>
                            </div>
                            @endif

                            @if($scholarship->specialization)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h-4a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                <span>{{ $scholarship->specialization->specialization_name }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- رابط التفاصيل -->
                        <div class="mt-5">
                            <a href="{{ route('scholarships.show', $scholarship->id) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
                                عرض التفاصيل
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-lg shadow">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h2 class="mt-4 text-xl font-semibold text-gray-700">لا توجد منح دراسية بعد</h2>
                    <p class="mt-2 text-gray-500">كن أول من يضيف منحة جديدة! اضغط على "إضافة منحة جديدة" أعلاه.</p>
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>