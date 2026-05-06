<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الفئات</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 30px auto; background: white; border-radius: 12px; padding: 25px; }
        h1 { color: #1e3a8a; }
        a { text-decoration: none; color: #2563eb; }
        .btn-add { background: #1e3a8a; color: white; padding: 10px 16px; border-radius: 8px; display: inline-block; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: right; border-bottom: 1px solid #ddd; }
        th { background: #eef2ff; }
        .actions a { margin-left: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>📂 قائمة الفئات</h1>
    <a href="{{ route('categories.create') }}" class="btn-add">+ إضافة فئة جديدة</a>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr><th>#</th><th>اسم الفئة</th><th>الإجراءات</th></tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td class="actions">
                        <a href="{{ route('categories.show', $category->id) }}">عرض</a>
                        <a href="{{ route('categories.edit', $category->id) }}">تعديل</a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('هل أنت متأكد؟')" style="background:none; border:none; color:red; cursor:pointer;">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">لا توجد فئات بعد.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>