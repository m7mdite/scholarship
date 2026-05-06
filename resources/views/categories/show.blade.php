<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض الفئة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 50px auto; background: white; border-radius: 12px; padding: 30px; }
        h1 { color: #1e3a8a; }
        .info { margin: 20px 0; padding: 10px; background: #f8fafc; border-radius: 8px; }
        .back-link { display: inline-block; margin-top: 20px; color: #1e3a8a; }
    </style>
</head>
<body>
<div class="container">
    <h1>📌 تفاصيل الفئة</h1>
    <div class="info">
        <p><strong>المعرف:</strong> {{ $category->id }}</p>
        <p><strong>اسم الفئة:</strong> {{ $category->category_name }}</p>
    </div>
    <a href="{{ route('categories.index') }}" class="back-link">← العودة إلى القائمة</a>
</div>
</body>
</html>