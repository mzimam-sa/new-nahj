<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>درجات الطالب</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; }
        .header { font-size: 20px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #333; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">تقرير درجات الطالب</div>
    <table class="table">
        <tr><th>اسم الطالب</th><td>{{ $student_name }}</td></tr>
        <tr><th>اسم الفصل</th><td>{{ $webinar_title }}</td></tr>
        <tr><th>الدرجة</th><td>{{ $score }}</td></tr>
        <tr><th>درجة النجاح</th><td>{{ $success_score }}</td></tr>
        <tr><th>الترم</th><td>{{ $term }}</td></tr>
        <tr><th>النوع</th><td>{{ $type }}</td></tr>
        <tr><th>ملاحظات</th><td>{{ $notes }}</td></tr>
    </table>
</body>
</html>
