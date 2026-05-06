<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $scholarship->scholarship_name }} | منصة المنح</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .prose strong { color: #1e3a8a; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <!-- رابط العودة -->
        <div class="mb-6">
            <a href="{{ route('scholarships.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                العودة إلى قائمة المنح
            </a>
        </div>

        <!-- بطاقة المنحة الرئيسية -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- رأس البطاقة مع خلفية مميزة -->
            <div class="bg-gradient-to-l from-blue-700 to-indigo-800 px-6 py-8 text-white">
                <div class="flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $scholarship->scholarship_name }}</h1>
                        <div class="flex flex-wrap gap-3 mt-3">
                            @if($scholarship->degree)
                                <span class="bg-white/20 backdrop-blur-sm text-sm px-3 py-1 rounded-full">{{ $scholarship->degree }}</span>
                            @endif
                            @if($scholarship->finance)
                                <span class="bg-white/20 backdrop-blur-sm text-sm px-3 py-1 rounded-full">{{ $scholarship->finance }}</span>
                            @endif
                            @if($scholarship->scholarship_language)
                                <span class="bg-white/20 backdrop-blur-sm text-sm px-3 py-1 rounded-full">🌐 {{ $scholarship->scholarship_language }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-left">
                        <div class="text-sm opacity-80">تاريخ النشر</div>
                        <div class="font-semibold">{{ \Carbon\Carbon::parse($scholarship->scholarship_created_at ?? $scholarship->created_at)->format('Y/m/d') }}</div>
                    </div>
                </div>
            </div>

            <!-- محتوى البطاقة -->
            <div class="p-6 md:p-8 space-y-8">
                <!-- معلومات أساسية (شبكة) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b pb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="font-semibold text-gray-700">الدولة / المدينة</h3>
                            <p>{{ $scholarship->country->country_name ?? 'غير محدد' }} @if($scholarship->city) - {{ $scholarship->city->city_name }} @endif</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="font-semibold text-gray-700">تاريخ البدء / الانتهاء</h3>
                            <p>{{ $scholarship->start_date ?? 'غير محدد' }} → {{ $scholarship->finished_date ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h-4a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        <div>
                            <h3 class="font-semibold text-gray-700">التخصص</h3>
                            <p>{{ $scholarship->specialization->specialization_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <div>
                            <h3 class="font-semibold text-gray-700">الفئة</h3>
                            <p>{{ $scholarship->category->category_name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- وصف المنحة -->
                @if($scholarship->scholarship_description)
                <div>
                    <h2 class="text-xl font-bold text-gray-800 border-r-4 border-blue-600 pr-3 mb-3">📖 الوصف</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $scholarship->scholarship_description }}</p>
                </div>
                @endif

                <!-- كيفية التقديم -->
                @if($scholarship->howToApply)
                <div>
                    <h2 class="text-xl font-bold text-gray-800 border-r-4 border-blue-600 pr-3 mb-3">📋 كيفية التقديم</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $scholarship->howToApply->how_to_apply_description }}</p>
                    </div>
                </div>
                @endif

                <!-- معايير التقديم -->
                @if($scholarship->applicationCriteria && $scholarship->applicationCriteria->count())
                <div>
                    <h2 class="text-xl font-bold text-gray-800 border-r-4 border-blue-600 pr-3 mb-3">✅ معايير التقديم</h2>
                    <div class="space-y-3">
                        @foreach($scholarship->applicationCriteria as $criteria)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex flex-wrap gap-2 mb-1">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">{{ $criteria->requirment_type }}</span>
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">{{ $criteria->application_criteria_value }}</span>
                            </div>
                            <p class="text-gray-600 text-sm">{{ $criteria->application_criteria_description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- الصور (إن وجدت) -->
                @if($scholarship->photos && $scholarship->photos->count())
                <div>
                    <h2 class="text-xl font-bold text-gray-800 border-r-4 border-blue-600 pr-3 mb-3">🖼️ صور المنحة</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($scholarship->photos as $photo)
                        <img src="{{ asset($photo->image_path) }}" alt="صورة" class="w-full h-32 object-cover rounded-lg shadow">
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- الخبرات الشخصية -->
                @if($scholarship->personalExperiences && $scholarship->personalExperiences->count())
                <div>
                    <h2 class="text-xl font-bold text-gray-800 border-r-4 border-blue-600 pr-3 mb-3">💬 تجارب شخصية</h2>
                    <div class="space-y-3">
                        @foreach($scholarship->personalExperiences as $exp)
                        <div class="bg-gray-50 p-4 rounded-lg italic text-gray-600">
                            “{{ $exp->personal_experiances_description }}”
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- زر تنبيه أو إضافة للمفضلة (اختياري) -->
                <div class="flex justify-center pt-4">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded-full shadow-md transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        تابع هذه المنحة
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>