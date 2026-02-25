<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة إتمام - {{ $webinar->translate('ar')?->title ?? $webinar->slug ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f0ece4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .page-wrap {
            width: 100%;
            max-width: 860px;
        }

        .certificate {
            background: #fff;
            border: 2px solid #c8a96e;
            padding: 60px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }

        .certificate::before, .certificate::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #c8a96e;
            border-style: solid;
        }
        .certificate::before { top: 12px; right: 12px; border-width: 2px 2px 0 0; }
        .certificate::after  { bottom: 12px; left: 12px; border-width: 0 0 2px 2px; }

        .corner-tl, .corner-br {
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #c8a96e;
            border-style: solid;
        }
        .corner-tl { top: 12px; left: 12px; border-width: 2px 0 0 2px; }
        .corner-br { bottom: 12px; right: 12px; border-width: 0 2px 2px 0; }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            font-size: 13px;
            color: #888;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 20px 0;
        }
        .divider-line { flex: 1; height: 1px; background: #c8a96e; opacity: 0.5; }
        .divider-diamond {
            width: 8px; height: 8px;
            background: #c8a96e;
            transform: rotate(45deg);
        }

        .cert-title {
            font-size: 36px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .cert-subtitle {
            font-size: 14px;
            color: #888;
            letter-spacing: 2px;
        }

        .body {
            text-align: center;
            padding: 30px 0;
        }

        .presented-to {
            font-size: 14px;
            color: #888;
            margin-bottom: 12px;
        }

        .completion-text {
            font-size: 16px;
            color: #444;
            line-height: 1.8;
            max-width: 600px;
            margin: 0 auto 24px;
        }

        .course-name {
            font-size: 28px;
            font-weight: 800;
            color: #c8a96e;
            background: #faf7f0;
            border: 1px solid #e8dcc8;
            padding: 20px 40px;
            display: inline-block;
            margin: 16px 0;
            line-height: 1.4;
        }

        .course-desc {
            font-size: 14px;
            color: #777;
            max-width: 600px;
            margin: 16px auto 0;
            line-height: 1.8;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e8dcc8;
        }

        .footer-item {
            text-align: center;
        }

        .footer-label {
            font-size: 11px;
            color: #aaa;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .footer-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .seal {
            width: 90px;
            height: 90px;
            border: 2px solid #c8a96e;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #c8a96e;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
            line-height: 1.6;
        }

        .cert-id {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #bbb;
            letter-spacing: 1px;
        }

        @media print {
            body { background: white; padding: 0; }
            .certificate { box-shadow: none; }
            .print-btn { display: none; }
        }

        .print-btn {
            display: block;
            margin: 24px auto 0;
            padding: 12px 40px;
            background: #1a1a2e;
            color: white;
            border: none;
            font-family: 'Tajawal', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 1px;
        }
        .print-btn:hover { background: #c8a96e; }
    </style>
</head>
<body>
<div class="page-wrap">
    <div class="certificate">
        <div class="corner-tl"></div>
        <div class="corner-br"></div>

        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <div class="divider">
                <div class="divider-line"></div>
                <div class="divider-diamond"></div>
                <div class="divider-line"></div>
            </div>
            <div class="cert-title">شهادة إتمام</div>
            <div class="cert-subtitle">CERTIFICATE OF COMPLETION</div>
            <div class="divider">
                <div class="divider-line"></div>
                <div class="divider-diamond"></div>
                <div class="divider-line"></div>
            </div>
        </div>

        <div class="body">
            <div class="presented-to">تُمنح هذه الشهادة لإتمام الدورة التدريبية</div>
            <div class="course-name">
                {{ $webinar->translate('ar')?->title ?? $webinar->slug ?? 'اسم الدورة' }}
            </div>
            @php
                $desc = strip_tags($webinar->translate('ar')?->description ?? '');
            @endphp
            @if($desc)
            <div class="course-desc">{{ Str::limit($desc, 200) }}</div>
            @endif
        </div>

        <div class="footer">
            <div class="footer-item">
                <div class="footer-label">عدد الساعات</div>
                <div class="footer-value">
                    {{ $webinar->number_of_hours ? $webinar->number_of_hours . ' ساعة' : '—' }}
                </div>
            </div>

            <div class="seal">
                <div>✦</div>
                <div>معتمد</div>
                <div>CERTIFIED</div>
            </div>

            <div class="footer-item">
                <div class="footer-label">المدرب</div>
                <div class="footer-value">
                    {{ $webinar->teacher?->full_name ?? '—' }}
                </div>
            </div>
        </div>

        <div class="cert-id">
            رقم الدورة: COURSE-{{ $webinar->id }}
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ طباعة</button>
</div>
</body>
</html>