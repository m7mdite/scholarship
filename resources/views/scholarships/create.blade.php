<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منحة جديدة | منصة المنح</title>
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 (مجاني) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- تخصيص بسيط لتحسين المظهر -->
    <style>
        /* تحسين مظهر الحقول التي بها خطأ */
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="max-w-5xl mx-auto px-4 py-8">
        <!-- بطاقة النموذج -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- رأس الصفحة -->
            <div class="bg-gradient-to-l from-blue-600 to-indigo-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                        <span>إضافة منحة دراسية جديدة</span>
                    </h1>
                    <a href="{{ route('scholarships.index') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
                <p class="text-blue-100 text-sm mt-2">يرجى ملء المعلومات التالية بعناية.</p>
            </div>

            <!-- النموذج -->
            <form action="{{ route('scholarships.store') }}" method="POST" class="p-6 space-y-8">
                @csrf

                <!-- رسائل الأخطاء العامة -->
                @if(isset($errors) && $errors->any())
                    <div class="bg-red-50 border-r-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                            <div>
                                <h3 class="font-semibold text-red-800">يوجد خطأ في الإدخال</h3>
                                <ul class="list-disc list-inside text-red-700 text-sm mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- == معلومات أساسية == -->
                <div class="bg-gray-50 p-5 rounded-xl">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i> معلومات أساسية
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- اسم المنحة -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                اسم المنحة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="scholarship_name" value="{{ old('scholarship_name') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('scholarship_name') input-error @enderror">
                            @error('scholarship_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الدرجة العلمية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدرجة العلمية <span class="text-red-500">*</span></label>
                            <select name="degree" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 @error('degree') input-error @enderror">
                                <option value="">اختر الدرجة</option>
                                <option value="بكالوريوس" {{ old('degree') == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                <option value="ماجستير" {{ old('degree') == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                                <option value="دكتوراه" {{ old('degree') == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                                <option value="دبلوم" {{ old('degree') == 'دبلوم' ? 'selected' : '' }}>دبلوم</option>
                                <option value="تدريب" {{ old('degree') == 'تدريب' ? 'selected' : '' }}>تدريب</option>
                            </select>
                            @error('degree') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- الجهة المانحة -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الجهة المانحة</label>
                            <input type="text" name="donar" value="{{ old('donar') }}"
                                   class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- لغة المنحة -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">لغة المنحة</label>
                            <input type="text" name="scholarship_language" value="{{ old('scholarship_language') }}" placeholder="مثل: إنجليزي، عربي"
                                   class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- نوع التمويل -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع التمويل</label>
                            <input type="text" name="finance" value="{{ old('finance') }}" placeholder="ممولة بالكامل، جزئية..."
                                   class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- وصف المنحة -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">وصف المنحة</label>
                            <textarea name="scholarship_description" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('scholarship_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- == التواريخ == -->
                <div class="bg-gray-50 p-5 rounded-xl">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-green-600"></i> التواريخ
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ البدء</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الانتهاء</label>
                            <input type="date" name="finished_date" value="{{ old('finished_date') }}" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الإضافة (اختياري)</label>
                            <input type="date" name="scholarship_created_at" value="{{ old('scholarship_created_at', date('Y-m-d')) }}" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                </div>

                <!-- == المواقع والتصنيف == -->
                <div class="bg-gray-50 p-5 rounded-xl">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-red-500"></i> الموقع والتصنيف
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدولة <span class="text-red-500">*</span></label>
                            <select name="country_id" required class="w-full rounded-lg border-gray-300 @error('country_id') input-error @enderror">
                                <option value="">اختر الدولة</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                            @error('country_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المدينة <span class="text-red-500">*</span></label>
                            <select name="city_id" required class="w-full rounded-lg border-gray-300 @error('city_id') input-error @enderror">
                                <option value="">اختر المدينة</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->city_name }}</option>
                                @endforeach
                            </select>
                            @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">التخصص <span class="text-red-500">*</span></label>
                            <select name="specialization_id" required class="w-full rounded-lg border-gray-300 @error('specialization_id') input-error @enderror">
                                <option value="">اختر التخصص</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>{{ $spec->specialization_name }}</option>
                                @endforeach
                            </select>
                            @error('specialization_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الفئة <span class="text-red-500">*</span></label>
                            <select name="category_id" required class="w-full rounded-lg border-gray-300 @error('category_id') input-error @enderror">
                                <option value="">اختر الفئة</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="{{ route('scholarships.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-times ml-1"></i> إلغاء
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition flex items-center gap-2">
                        <i class="fas fa-save"></i> حفظ المنحة
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>