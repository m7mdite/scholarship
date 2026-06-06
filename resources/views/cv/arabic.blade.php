<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $full_name }} - سيرة ذاتية</title>
    <style>
        /* === إعدادات عامة === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Cairo', 'Tajawal', sans-serif;
            background: #eef2f5;
            padding: 25pt;
            color: #1f2d3d;
        }

        /* بطاقة السيرة الذاتية الرئيسية */
        .resume {
            max-width: 1050px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
            transition: all 0.2s;
        }

        /* الهيدر مع تدرج لوني أنيق */
        .header {
            background: linear-gradient(135deg, #0F2B3D 0%, #1C4E70 100%);
            padding: 28pt 32pt;
            color: white;
            text-align: center;
        }

        .name {
            font-size: 28pt;
            font-weight: 800;
            letter-spacing: -0.2px;
            margin-bottom: 6px;
        }

        .title {
            font-size: 13pt;
            font-weight: 500;
            opacity: 0.85;
            border-top: 1px solid rgba(255,255,255,0.25);
            display: inline-block;
            padding-top: 8px;
            margin-top: 4px;
        }

        /* شبكة ذات عمودين باستخدام Grid (أكثر حداثة) */
        .resume-grid {
            display: grid;
            grid-template-columns: 32% 68%;
        }

        /* العمود الجانبي */
        .sidebar {
            background: #F8FAFE;
            padding: 24px 20px;
            border-left: 1px solid #E2E8F0;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #0F2B3D;
            margin: 18px 0 12px 0;
            border-right: 3px solid #E6B422;
            padding-right: 10px;
            letter-spacing: -0.2px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 9.5pt;
            margin-bottom: 10px;
            color: #2c3e4e;
        }

        .contact-icon {
            font-weight: 600;
            color: #E6B422;
            width: 24px;
        }

        /* تنسيق المهارات كشبكة (بطاقات) */
        .skills-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 5px;
        }

        .skill-badge {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 40px;
            padding: 5px 12px;
            font-size: 9pt;
            color: #1f3a4b;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            transition: 0.1s;
        }

        /* اللغات مع نقاط */
        .lang-list {
            list-style: none;
            padding-right: 0;
        }
        .lang-list li {
            margin-bottom: 6px;
            font-size: 9.5pt;
            position: relative;
            padding-right: 14px;
        }
        .lang-list li::before {
            content: "▹";
            position: absolute;
            right: 0;
            color: #E6B422;
        }

        /* العمود الرئيسي */
        .main {
            padding: 24px 28px;
        }

        .main-title {
            font-size: 16px;
            font-weight: 800;
            color: #0F2B3D;
            border-bottom: 2px solid #E6B422;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 15px;
            letter-spacing: -0.2px;
        }

        .bio-text {
            background: #FFFFFF;
            padding: 15px;
            border-radius: 14px;
            line-height: 1.55;
            font-size: 9.8pt;
            text-align: justify;
            margin-bottom: 25px;
            border: 1px solid #E9EDF2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        /* تذييل بسيط وأنيق */
        .footer {
            background: #F1F5F9;
            text-align: center;
            padding: 12px;
            font-size: 8.5pt;
            color: #4A5B6E;
            border-top: 1px solid #E2E8F0;
        }

        /* تجنب انكسار الصفحة */
        .resume, .resume-grid, .sidebar, .main {
            page-break-inside: avoid;
        }

        /* استجابة للشاشات الصغيرة (للمعاينة) */
        @media (max-width: 700px) {
            .resume-grid {
                grid-template-columns: 1fr;
            }
            body {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
<div class="resume">
    <div class="header">
        <div class="name">{{ $full_name }}</div>
        <div class="title">{{ $specialization }}</div>
    </div>

    <div class="resume-grid">
        <!-- الجانب الأيمن (في RTL) -->
        <div class="sidebar">
            <div class="section-title">معلومات الاتصال</div>
            <div class="contact-item"><span class="contact-icon">✉️</span> {{ $email }}</div>
            @if($phone)<div class="contact-item"><span class="contact-icon">📞</span> {{ $phone }}</div>@endif
            @if($age)<div class="contact-item"><span class="contact-icon">🎂</span> {{ $age }} سنة</div>@endif

            <div class="section-title">المهارات الأساسية</div>
            <div class="skills-grid">
                @foreach($skills as $skill)
                    <span class="skill-badge">{{ $skill }}</span>
                @endforeach
            </div>

            <div class="section-title">اللغات</div>
            <ul class="lang-list">
                @foreach($languages as $lang)
                    <li>{{ $lang }}</li>
                @endforeach
            </ul>
        </div>

        <!-- القسم الرئيسي -->
        <div class="main">
            <div class="main-title">نبذة مهنية</div>
            <div class="bio-text">{{ $bio }}</div>

            <!-- يمكن إضافة أقسام إضافية: الخبرات، التعليم، الشهادات -->
        </div>
    </div>
    <div class="footer">
        السيرة الذاتية - منصة المنح الدراسية
    </div>
</div>
</body>
</html>