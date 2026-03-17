@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .grade-entry-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 10px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .grade-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .grade-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .section-header i {
            font-size: 28px;
            color: #667eea;
        }
        
        .section-header h4 {
            margin: 0;
            color: #2d3748;
            font-weight: 700;
            font-size: 22px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }
        
        .form-group label i {
            color: #667eea;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: px 16px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-control:hover {
            border-color: #cbd5e0;
        }
        
        .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23667eea' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 12px center;
            background-size: 16px 12px;
        }
        
        .students-table-wrapper {
            background: #f7fafc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .table-modern {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .table-modern thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table-modern thead th {
            padding: 16px 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            text-align: center;
        }
        
        .table-modern tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table-modern tbody tr:hover {
            background: #f7fafc;
            transform: scale(1.01);
        }
        
        .table-modern tbody td {
            padding: 14px 10px;
            vertical-align: middle;
            text-align: center;
        }
          
        .table-modern input[type="number"],
        .table-modern input[type="text"],
        .table-modern select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
            width: 100%;
            transition: all 0.2s ease;
            font-size: 13px;
        }
        
        .table-modern input:focus,
        .table-modern select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .checkbox-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }
        
        .btn-save:active {
            transform: translateY(0);
        }
        
        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ebf4ff;
            color: #2c5282;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-top: 10px;
        }
        
        .info-badge i {
            color: #4299e1;
        }
        
        .loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #667eea;
            font-weight: 500;
        }
        
        .loading-state i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #718096;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #cbd5e0;
            margin-bottom: 15px;
        }
        
        .error-state {
            text-align: center;
            padding: 30px;
            color: #e53e3e;
        }
        
        .error-state i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .student-name {
            font-weight: 600;
            color: #2d3748;
        }
        
        .student-index {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }
        
        @media (max-width: 768px) {
            .grade-entry-wrapper {
                padding: 20px 10px;
            }
            
            .grade-card {
                padding: 20px;
            }
            
            .table-responsive {
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="grade-entry-wrapper">
        <section class="grade-card">
            <form action="{{ url('/panel/term_grades') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="section-header">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>رفع ملف درجات الطالب</h4>
                </div>

                @if(request('student_id'))
                    <input type="hidden" name="grades[{{ request('student_id') }}][student_id]" value="{{ request('student_id') }}">
                    <div class="form-group">
                                <form>
                        <input type="file" name="grades[{{ request('student_id') }}][pdf_file]" accept="application/pdf" class="form-control" required>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                                        <input type="file" name="grades[{{ request('student_id') }}][pdf_file]" accept="application/pdf" class="form-control pdf-file-input" disabled>
                    </div>
                @endif

                <div class="mt-4 text-center">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>
                        حفظ جميع الدرجات
                    </button>
                </div>
            </form>
        </section>
    </div>

    @push('scripts_bottom')
        <script>
        // اجعل required فقط إذا كان التشييك مفعّل
        function togglePdfRequired(checkbox) {
            const row = checkbox.closest('tr');
            const pdfInput = row.querySelector('.pdf-file-input');
            if (checkbox.checked) {
                pdfInput.setAttribute('required', 'required');
            } else {
                pdfInput.removeAttribute('required');
            }
        }
        (function () {
                const webinarSelect = document.getElementById('webinar-select');
                const studentsTbody = document.getElementById('students-tbody');
                const baseUrl = "{{ url('/panel/webinars') }}";

                function renderStudents(students) {
                    if (!Array.isArray(students) || students.length === 0) {
                        studentsTbody.innerHTML = `
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash"></i>
                                        <p>لا يوجد طلاب مسجلين في هذا الفصل</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                                            <textarea name="notes" class="form-control" rows="4" placeholder="أضف ملاحظاتك هنا..." readonly></textarea>
                    let html = '';
                    students.forEach((s, idx) => {
                                        <button type="button" class="btn btn-primary" disabled>حفظ</button>
                        const name = s.name || s.full_name || s.username || ('#' + id);
                        html += `
    <tr>
        <td><span class="student-index">${idx + 1}</span></td>
        <td class="student-name">${escapeHtml(name)}</td>
        <input type="hidden" name="grades[${id}][student_id]" value="${id}">
        <td><input type="text" name="grades[${id}][notes]" class="form-control" value="${s.notes ? escapeHtml(s.notes) : ''}"></td>
        <td>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="grades[${id}][enabled]" value="1" checked>
            </div>
        </td>
        <td>
            <input type="file" name="grades[${id}][pdf_file]" accept="application/pdf" class="form-control pdf-file-input">
            ${(s.pdf_path ? `<a href="/store/${s.pdf_path}" target="_blank" style="margin-right:8px;display:inline-block;">ملف الطالب الحالي</a>` : '')}
        </td>
    </tr>
`;
                    });

                    studentsTbody.innerHTML = html;
                    // بعد رسم الجدول، أضف روابط الملفات الحالية إن وجدت
                    (students || []).forEach((s) => {
                        if (s.pdf_path) {
                            const linkSpan = document.getElementById('pdf-link-' + s.id);
                            if (linkSpan) {
                                linkSpan.innerHTML = `<a href="/storage/${s.pdf_path}" target="_blank" style="margin-right:8px;">ملف الطالب الحالي</a>`;
                            }
                        }
                    });
                }

                function escapeHtml(str) {
                    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#39;',
                            '`': '&#96;',
                            '=': '&#61;',
                            '/': '&#47;'
                        }[s];
                    });
                }

                webinarSelect.addEventListener('change', function () {
    const webinarId = this.value;
    
    if (!webinarId) {
        studentsTbody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <i class="fas fa-arrow-up"></i>
                        <p>الرجاء اختيار الفصل الدراسي لعرض قائمة الطلاب</p>
                    </div>
                </td>
            </tr>
        `;
        return; // ← هاد مهم
    }

    studentsTbody.innerHTML = `
        <tr>
            <td colspan="9">
                <div class="loading-state">
                    <i class="fas fa-spinner"></i>
                    <span>جارٍ تحميل بيانات الطلاب...</span>
                </div>
            </td>
        </tr>
    `;

    fetch(baseUrl + '/' + webinarId + '/students', {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(r => r.ok ? r.json() : Promise.reject(r))
    .then(data => {
        setTimeout(() => renderStudents(data), 300);
    })
    .catch(() => {
        studentsTbody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="error-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>فشل في تحميل بيانات الطلاب. يرجى المحاولة مرة أخرى.</p>
                    </div>
                </td>
            </tr>
        `;
    });
});
            })();
        </script>
    @endpush
@endsection