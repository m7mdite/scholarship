<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسالة توصية</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Cairo', 'Tajawal', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #1f2d3d;
        }
        .letter {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: right;
            margin-bottom: 20px;
        }
        .date {
            text-align: left;
            margin-bottom: 15px;
        }
        .subject {
            font-weight: bold;
            margin: 15px 0 10px;
        }
        .signature {
            margin-top: 35px;
        }
        .footer-note {
            font-size: 8pt;
            color: #5a6e7c;
            margin-top: 25px;
            border-top: 1px solid #ddd;
            padding-top: 6px;
            text-align: center;
        }
        p {
            margin: 0 0 8px 0;
            text-align: justify;
        }
        .recipient {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="letter">
    <div class="header">
        <strong>الدكتور/الأستاذ: {{ $doctor_name }}</strong><br>
        البريد الإلكتروني: {{ $doctor_email }}<br>
        <em>أستاذ مشارك – قسم {{ $specialization }}</em>
    </div>

    <div class="date">
        التاريخ: {{ $date }}
    </div>

    <div class="recipient">
        إلى: لجنة المنح الدراسية / سفارة .........<br>
        السلام عليكم ورحمة الله وبركاته،
    </div>

    <div class="subject">
        الموضوع: خطاب توصية للطالب {{ $student_name }}
    </div>

    <p>
        يشرفني أن أوصي الطالب <strong>{{ $student_name }}</strong> (العمر {{ $student_age }} سنة) بكل ثقة وتقدير، وذلك لالتحاقه ببرنامج {{ $scholarship_name }} في تخصص <strong>{{ $specialization }}</strong>.
    </p>

    <p>
        عرفت الطالب {{ $student_name }} خلال فترة تدريسه في جامعة {{ $university }}، حيث أظهر تفوقاً أكاديمياً لافتاً، وقدرة على التحليل وحل المشكلات، واجتهاداً في البحث. يتمتع بأخلاق عالية، ومهارات تواصل متميزة، وقدرة على العمل ضمن فريق.
    </p>

    <p>
        كما تميز بمشروع التخرج الذي أشرفت عليه، والذي تناول تطوير نظام مبتكر في مجال تخصصه. كان الطالب ملتزماً بالمواعيد، دقيقاً في عمله، ومبادراً في اقتراح الحلول.
    </p>

    <p>
        أنا على يقين بأن {{ $student_name }} سيكون إضافة قيمة لبرنامجكم الأكاديمي، وسيحقق نجاحات متميزة تعود بالفائدة على جامعتكم ومجتمعه. أوصي به بقوة دون أي تحفظ.
    </p>

    <p>
        يمكنكم التواصل معي لمزيد من المعلومات عن الطالب عبر البريد الإلكتروني أعلاه.
    </p>

    <div class="signature">
        وتفضلوا بقبول فائق الاحترام،<br>
        <strong>{{ $doctor_name }}</strong><br>
        (التوقيع) ...........................
    </div>

    <div class="footer-note">
        صدرت هذه الرسالة بناءً على معرفة أكاديمية مباشرة – منصة المنح الدراسية
    </div>
</div>
</body>
</html>