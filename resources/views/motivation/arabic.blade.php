<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسالة تحفيزية للسفارة</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Cairo', 'Tajawal', sans-serif;
            line-height: 1.6;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            color: #1f2d3d;
        }
        .letter {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: right;
            margin-bottom: 30px;
        }
        .date {
            text-align: left;
            margin-bottom: 20px;
        }
        .subject {
            font-weight: bold;
            margin: 20px 0 10px;
        }
        .signature {
            margin-top: 40px;
        }
        .footer-note {
            font-size: 9pt;
            color: #5a6e7c;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="letter">
    <div class="header">
        <strong>{{ $full_name }}</strong><br>
        @if($email) البريد الإلكتروني: {{ $email }}<br>@endif
        @if($phone) الهاتف: {{ $phone }}<br>@endif
        العمر: {{ $age }} سنة
    </div>

    <div class="date">
        التاريخ: {{ $date }}
    </div>

    <div style="margin-bottom: 15px;">
        إلى: سفارة <strong>{{ $country }}</strong><br>
        قسم التأشيرات<br>
        السلام عليكم ورحمة الله وبركاته،
    </div>

    <div class="subject">
        الموضوع: طلب الحصول على تأشيرة طالب – منحة {{ $scholarship_name }}
    </div>

    <p>
        أنا الموقع أدناه <strong>{{ $full_name }}</strong>، أتقدم لسفارتكم الكريمة بطلب الحصول على تأشيرة دراسة لمتابعة دراستي العليا في <strong>{{ $university }}</strong>، وذلك في إطار حصولي على منحة <strong>{{ $scholarship_name }}</strong> لدراسة تخصص <strong>{{ $specialization }}</strong>.
    </p>

    <p>
        لطالما كان لدي حلم أكاديمي في التخصص المذكور، وقد وجدت في برنامج المنحة هذا فرصة مثالية لتحقيقه. اخترت <strong>{{ $country }}</strong> تحديداً لما تتمتع به من سمعة أكاديمية رفيعة، وجامعة <strong>{{ $university }}</strong> التي تعد من أعرق الجامعات في هذا المجال.
    </p>

    <p>
        بعد حصولي على الدرجة العلمية، أخطط للعودة إلى بلدي والمساهمة في تطوير القطاع التعليمي/الصحي/التقني (حسب التخصص) ونقل الخبرات التي اكتسبها. كما أنني أرتبط بعلاقات قوية مع عائلتي ووظيفتي الحالية، مما يجعل عودتي أمراً محتوماً.
    </p>

    <p>
        المنحة التي حصلت عليها تغطي كامل الرسوم الدراسية وتكاليف المعيشة، مما يؤكد قدرتي المالية على الإقامة القانونية دون الحاجة إلى العمل غير المرخص. سألتزم بجميع قوانين التأشيرة وأغادر الأراضي {{ $country }} فور انتهاء دراستي.
    </p>

    <p>
        أرفق مع هذه الرسالة مستنداتي الداعمة (خطاب المنحة، القبول الجامعي، كشف الدرجات، إلخ). آمل منكم قبول طلبي والتكرم بإصدار التأشيرة.
    </p>

    <div class="signature">
        وتفضلوا بقبول فائق الاحترام،<br>
        {{ $full_name }}<br>
        التوقيع: ...................
    </div>

    <div class="footer-note">
        تم إنشاء هذه الرسالة بواسطة منصة المنح الدراسية – نموذج احترافي للتقديم على السفارات.
    </div>
</div>
</body>
</html>