<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Tajawal',sans-serif;}</style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-blue-800">تسجيل الدخول</h2>
                <p class="text-gray-600">مرحباً بك في منصة المنح الدراسية</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-3 mb-4 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-1">كلمة المرور</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">تسجيل الدخول</button>
            </form>
            <p class="text-center text-gray-600 mt-4">
                ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-blue-600 hover:underline">إنشاء حساب جديد</a>
            </p>
        </div>
    </div>
</body>
</html>