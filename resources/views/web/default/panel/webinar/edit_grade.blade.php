<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل سجل الدرجات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            direction: rtl;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .panel-section-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-section {
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 10px 18px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background: #667eea;
            color: white;
            transform: translateX(5px);
            border-color: #667eea;
        }

        .back-button svg {
            transition: transform 0.3s ease;
        }

        .back-button:hover svg {
            transform: translateX(3px);
        }

        h3 {
            font-size: 32px;
            color: #2d3748;
            position: relative;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h3::before {
            content: '📝';
            font-size: 36px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .info-card {
            background: linear-gradient(135deg, #f6f8fb 0%, #e9ecf1 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 35px;
            border-right: 5px solid #667eea;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            font-size: 16px;
            color: #4a5568;
            position: relative;
            z-index: 1;
        }

        .info-item:not(:last-child) {
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item strong {
            color: #2d3748;
            margin-left: 10px;
            min-width: 80px;
            font-weight: 600;
            position: relative;
        }

        .info-item strong::before {
            content: '•';
            color: #667eea;
            font-size: 20px;
            margin-left: 8px;
        }

        .form-group {
            margin-bottom: 25px;
            animation: slideIn 0.5s ease forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 15px;
            position: relative;
            padding-right: 20px;
        }

        label::before {
            content: '★';
            position: absolute;
            right: 0;
            color: #667eea;
            font-size: 14px;
            animation: sparkle 2s ease-in-out infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f7fafc;
            color: #2d3748;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control:hover {
            border-color: #cbd5e0;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 18px center;
            padding-left: 45px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 35px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 140px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary span {
            position: relative;
            z-index: 1;
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .panel-section-card {
                padding: 25px;
            }

            h3 {
                font-size: 26px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="panel-section-card">
            <div class="header-section">
                <a href="{{ route('panel.webinars.term_grades.index') }}" class="back-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    رجوع
                </a>
                <h3>تعديل الدرجة</h3>
            </div> 
        
            <div class="info-card">
                <div class="info-item">
                    <strong>الطالب:</strong>
                    <span>{{ optional($grade->student)->full_name ?? optional($grade->student)->name ?? '-' }}</span>
                </div>
            </div>

<form action="{{ url('/panel/grades/'.$grade->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

                <div class="form-group"> 
                    <label>الترم</label>
                    <input name="term" type="number" class="form-control" value="{{ old('term', $grade->term) }}" placeholder="أدخل رقم الترم">
                </div>

                <div class="form-group">
                    <label>ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="أضف ملاحظاتك هنا...">{{ old('notes', $grade->notes) }}</textarea>
                </div>

                <div class="form-group">
                    <label>سجل  الطالب</label>
                    @if($grade->pdf_path)
                        <div style="margin-bottom:8px">
                            <a href="/store/{{ $grade->pdf_path }}" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> تحميل الملف الحالي</a>
                        </div>
                    @endif
                    <input type="file" name="pdf_file" accept="application/pdf" class="form-control">
                    <small class="text-muted">يمكنك رفع ملف PDF جديد لاستبدال الملف الحالي.</small>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <span>✓ حفظ التعديلات</span>
                    </button>
                    <a href="{{ url('/panel/webinars/term_grades') }}" class="btn btn-secondary">
                        ✕ إلغاء
                    </a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>