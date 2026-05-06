<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة التخصصات</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 30px auto; background: white; border-radius: 12px; padding: 25px; }
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
    <h1>🎓 قائمة التخصصات</h1>
    <a href="{{ route('specializations.create') }}" class="btn-add">+ إضافة تخصص جديد</a>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr><th>#</th><th>اسم التخصص</th><th>الفئة</th><th>الإجراءات</th></tr>
        </thead>
        <tbody>
            @forelse($specializations as $specialization)
                <tr>
                    <td>{{ $specialization->id }}</td>
                    <td>{{ $specialization->specialization_name }}</td>
                    <td>{{ $specialization->category->category_name ?? '-' }}</td>
                    <td class="actions">
                        <a href="{{ route('specializations.show', $specialization->id) }}">عرض</a>
                        <a href="{{ route('specializations.edit', $specialization->id) }}">تعديل</a>
                        <form action="{{ route('specializations.destroy', $specialization->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('هل أنت متأكد؟')" style="background:none; border:none; color:red; cursor:pointer;">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">لا توجد تخصصات بعد.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>