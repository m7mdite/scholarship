<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة المنح الدراسية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        .hero-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        /* أحجام الأيقونات المخصصة */
        .icon-md {
            width: 24px;
            height: 24px;
        }

        .icon-lg {
            width: 32px;
            height: 32px;
        }

        .icon-xl {
            width: 48px;
            height: 48px;
        }

        /* أو إذا أردت تطبيقها على جميع الـ svg داخل عناصر معينة */
        .feature-icon {
            width: 48px;
            height: 48px;
        }

        .feature-icon svg {
            width: 24px;
            height: 24px;
        }
    </style>
    @viteReactRefresh
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-50">

    <div id="root"></div>

    <!-- الهيدر (شريط التنقل) -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-xl font-bold text-gray-800">منصة المنح</span>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('scholarships.index') }}" class="text-gray-600 hover:text-blue-600 transition">المنح</a>
                <a href="{{ route('countries.index') }}" class="text-gray-600 hover:text-blue-600 transition">الدول</a>
                <a href="{{ route('specializations.index') }}" class="text-gray-600 hover:text-blue-600 transition">التخصصات</a>
                <a href="{{ route('cities.index') }}" class="text-gray-600 hover:text-blue-600 transition">المدن</a>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition">حساب جديد</a>
            </div>
        </div>
    </nav>

    <!-- القسم الرئيسي (Hero) -->
    <section class="hero-bg text-white py-20 md:py-28">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4">اكتشف مستقبلك مع المنح الدراسية</h1>
            <p class="text-lg md:text-xl mb-8 opacity-90 max-w-2xl mx-auto">منصة متكاملة تجمع لك أفضل فرص المنح حول العالم، حسب تخصصك، بلدك، ومؤهلاتك.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('scholarships.index') }}" class="bg-white text-blue-700 font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition">استعرض المنح الآن</a>
                <a href="#featured-scholarships" class="bg-transparent border border-white py-3 px-6 rounded-lg hover:bg-white/10 transition">أحدث المنح</a>
            </div>
        </div>
    </section>

    <!-- مميزات المنصة -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">لماذا منصة المنح؟</h2>
                <p class="text-gray-500 mt-2">نوفر لك كل ما تحتاجه لبدء رحلتك الأكاديمية بثقة</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-xl hover:shadow-lg transition">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">بحث متقدم</h3>
                    <p class="text-gray-600">فلترة حسب الدولة، التخصص، الدرجة العلمية، ومصدر التمويل بسهولة.</p>
                </div>
                <div class="text-center p-6 rounded-xl hover:shadow-lg transition">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">معلومات موثوقة</h3>
                    <p class="text-gray-600">منح محدثة ومصادر رسمية، مع تفاصيل دقيقة عن شروط التقديم.</p>
                </div>
                <div class="text-center p-6 rounded-xl hover:shadow-lg transition">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">مفضلة وتنبيهات</h3>
                    <p class="text-gray-600">احفظ المنح التي تهمك واستلم إشعارات عند اقتراب موعد التقديم.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- أحدث المنح الدراسية (مثال مع بيانات ثابتة، يمكن ربطها بقاعدة البيانات لاحقاً) -->
    <section id="featured-scholarships" class="py-16 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-800">🎓 أحدث المنح الدراسية</h2>
                <p class="text-gray-500 mt-2">فرص مميزة محدثة باستمرار</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- يمكن استبدال هذا بجلب بيانات من الـ Controller -->
                <div class="bg-white rounded-xl shadow-md p-5">
                    <h3 class="font-bold text-lg">برنامج منح الحكومة التركية</h3>
                    <p class="text-gray-600 text-sm mt-2">تركيا - إسطنبول</p>
                    <p class="text-gray-500 text-sm">بكالوريوس، ماجستير، دكتوراه</p>
                    <a href="#" class="text-blue-600 text-sm mt-3 inline-block">عرض التفاصيل →</a>
                </div>
                <div class="bg-white rounded-xl shadow-md p-5">
                    <h3 class="font-bold text-lg">منحة تشيفنينغ البريطانية</h3>
                    <p class="text-gray-600 text-sm mt-2">بريطانيا - لندن</p>
                    <p class="text-gray-500 text-sm">ماجستير فقط</p>
                    <a href="#" class="text-blue-600 text-sm mt-3 inline-block">عرض التفاصيل →</a>
                </div>
                <div class="bg-white rounded-xl shadow-md p-5">
                    <h3 class="font-bold text-lg">منحة DAAD الألمانية</h3>
                    <p class="text-gray-600 text-sm mt-2">ألمانيا - برلين</p>
                    <p class="text-gray-500 text-sm">دكتوراه وبحوث</p>
                    <a href="#" class="text-blue-600 text-sm mt-3 inline-block">عرض التفاصيل →</a>
                </div>
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('scholarships.index') }}" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">جميع المنح</a>
            </div>
        </div>
    </section>

    <!-- إحصائيات سريعة -->
    <section class="bg-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-blue-600">+250</div>
                    <div class="text-gray-600">منحة دراسية</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600">50+</div>
                    <div class="text-gray-600">دولة حول العالم</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600">30+</div>
                    <div class="text-gray-600">تخصص أكاديمي</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600">15k+</div>
                    <div class="text-gray-600">طالب استفاد</div>
                </div>
            </div>
        </div>
    </section>

    <!-- دعوة للتسجيل -->
    <section class="py-16 bg-blue-50">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">انضم إلى آلاف الطلاب الذين وجدوا منحتهم المثالية</h2>
            <p class="text-gray-600 mb-6">سجل الآن لتتمكن من حفظ المنح والحصول على توصيات مخصصة لك.</p>
            <a href="{{ route('register') }}" class="bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:bg-blue-700 transition">إنشاء حساب مجاني</a>
        </div>
    </section>

    <!-- التذييل -->
    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; {{ date('Y') }} منصة المنح الدراسية. جميع الحقوق محفوظة.</p>
            <div class="flex justify-center gap-6 mt-4">
                <a href="#" class="hover:text-white transition">عن المنصة</a>
                <a href="#" class="hover:text-white transition">سياسة الخصوصية</a>
                <a href="#" class="hover:text-white transition">اتصل بنا</a>
            </div>
        </div>
    </footer>
</body>

</html>