<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recommendation Letter</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
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
            text-align: left;
            margin-bottom: 20px;
        }
        .date {
            text-align: right;
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
        <strong>Dr. / Prof. {{ $doctor_name }}</strong><br>
        Email: {{ $doctor_email }}<br>
        <em>Associate Professor – Department of {{ $specialization }}</em>
    </div>

    <div class="date">
        Date: {{ $date }}
    </div>

    <div class="recipient">
        To: Scholarship Committee / Embassy of .........<br>
        Dear Members,
    </div>

    <div class="subject">
        Subject: Letter of Recommendation for {{ $student_name }}
    </div>

    <p>
        It is my distinct pleasure to strongly recommend <strong>{{ $student_name }}</strong> (age {{ $student_age }}) for the <strong>{{ $scholarship_name }}</strong> program in the field of <strong>{{ $specialization }}</strong>.
    </p>

    <p>
        I have known {{ $student_name }} during his/her studies at {{ $university }}. He/She has demonstrated outstanding academic performance, strong analytical and problem-solving skills, and a genuine passion for research. His/Her ethical conduct, communication abilities, and teamwork spirit are exemplary.
    </p>

    <p>
        I supervised his/her graduation project, which focused on developing an innovative system in his/her specialization. The student was consistently punctual, meticulous, and proactive in proposing effective solutions.
    </p>

    <p>
        I am confident that {{ $student_name }} will be a valuable addition to your academic program and will achieve remarkable success. I recommend him/her without any reservation.
    </p>

    <p>
        Should you require further information, please do not hesitate to contact me via the email above.
    </p>

    <div class="signature">
        Sincerely yours,<br>
        <strong>{{ $doctor_name }}</strong><br>
        (Signature) ...........................
    </div>

    <div class="footer-note">
        This letter is issued based on direct academic acquaintance – Scholarship Platform
    </div>
</div>
</body>
</html>