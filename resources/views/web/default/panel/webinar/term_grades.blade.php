@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-grades-dashboard {
            padding: 40px 20px;
            border-radius: 20px;
            min-height: 100vh;
        }
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.1;
            transform: translate(30%, -30%);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        }
        .stat-content { position: relative; z-index: 1; }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
            color: white;
        }
        .stat-card.total-students .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.total-students::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-value { font-size: 32px; font-weight: 700; color: #2d3748; margin-bottom: 5px; }
        .stat-label { color: #718096; font-size: 14px; font-weight: 500; }
        .controls-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        .controls-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        .controls-title { display: flex; align-items: center; gap: 10px; }
        .controls-title i { font-size: 20px; color: #667eea; }
        .controls-title h4 { margin: 0; color: #2d3748; font-weight: 600; }
        .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            text-decoration: none;
        }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); color: white; }
        .action-btn.secondary { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .filter-group label { display: block; margin-bottom: 8px; color: #4a5568; font-weight: 600; font-size: 14px; }
        .filter-group select, .filter-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .filter-group select:focus, .filter-group input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        .grades-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        .section-title { display: flex; align-items: center; gap: 12px; }
        .section-title i { font-size: 24px; color: #667eea; }
        .section-title h4 { margin: 0; color: #2d3748; font-weight: 700; font-size: 20px; }
        .export-buttons { display: flex; gap: 8px; }
        .export-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .export-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
        .grades-table {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .grades-table thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .grades-table thead th { padding: 16px 12px; font-weight: 600; font-size: 14px; text-align: center; border: none; }
        .grades-table tbody tr { transition: all 0.2s ease; border-bottom: 1px solid #e2e8f0; }
        .grades-table tbody tr:hover { background: #f7fafc; }
        .grades-table tbody td { padding: 16px 12px; text-align: center; vertical-align: middle; }
        .student-info { display: flex; align-items: center; gap: 5px; justify-content: right; }
        .student-avatar-small {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 14px; font-weight: 600;
        }
        .student-name { font-weight: 600; color: #2d3748; }
        .term-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%;
            background: #667eea; color: white; font-weight: 700; font-size: 14px;
        }
        .action-cell { display: flex; gap: 8px; justify-content: center; }
        .btn-edit {
            background: #4299e1; color: white; border: none;
            padding: 6px 12px; border-radius: 6px; cursor: pointer;
            font-size: 12px; transition: all 0.2s ease;
        }
        .btn-edit:hover { background: #3182ce; transform: scale(1.05); }
        .btn-delete {
            background: #f56565; color: white; border: none;
            padding: 6px 12px; border-radius: 6px; cursor: pointer;
            font-size: 12px; transition: all 0.2s ease;
        }
        .btn-delete:hover { background: #e53e3e; transform: scale(1.05); }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 64px; color: #cbd5e0; margin-bottom: 20px; }
        .empty-state h5 { color: #4a5568; margin-bottom: 10px; }
        .empty-state p { color: #718096; font-size: 14px; }
        @media (max-width: 768px) {
            .admin-grades-dashboard { padding: 20px 10px; }
            .stats-cards { grid-template-columns: 1fr; }
            .filters-grid { grid-template-columns: 1fr; }
            .action-buttons { width: 100%; }
            .action-btn { flex: 1; }
            .section-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .export-buttons { width: 100%; }
            .export-btn { flex: 1; }
            .grades-table { font-size: 11px; }
            .grades-table th, .grades-table td { padding: 8px 4px; }
        }
    </style>
@endpush

@section('content')
  @if(session('success'))
        <div id="success-alert" style="background:#38c172;color:#fff;padding:16px 24px;border-radius:8px;text-align:center;font-weight:600;margin-bottom:18px;">
            <i class="fas fa-check-circle" style="margin-left:8px;"></i>
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function(){
                var alert = document.getElementById('success-alert');
                if(alert) alert.style.display = 'none';
            }, 3500);
        </script>
    @endif
    <div class="admin-grades-dashboard">

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card total-students">
                <div class="stat-content">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value" id="total-students">0</div>
                    <div class="stat-label">إجمالي الطلاب</div>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="controls-header">
                <div class="controls-title">
                    <i class="fas fa-sliders-h"></i>
                    <h4>البحث والتصفية</h4>
                </div>
                <div class="action-buttons">
                    <a href="/panel/webinars/add_term_grades" class="action-btn">
                        <i class="fas fa-plus"></i>
                        إضافة درجات جديدة
                    </a>
                    <button class="action-btn secondary" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i>
                        تحديث البيانات
                    </button>
                </div>
            </div>

            <div class="filters-grid">
                <div class="filter-group">
                    <label><i class="fas fa-user"></i> اسم الطالب</label>
                    <input type="text" id="filter-student" placeholder="ابحث عن طالب...">
                </div>
                <div class="filter-group" style="display:none">
                    <label><i class="fas fa-book"></i> المادة</label>
                    <select id="filter-course">
                        <option value="">جميع المواد</option>
                    </select>
                </div>
                <div class="filter-group" style="display:none">
                    <label><i class="fas fa-calendar-alt"></i> الترم</label>
                    <select id="filter-term">
                        <option value="">جميع الترمات</option>
                        <option value="1">الترم الأول</option>
                        <option value="2">الترم الثاني</option>
                        <option value="3">الترم الثالث</option>
                        <option value="4">الترم الرابع</option>
                    </select>
                </div>
                <div class="filter-group" style="display:none">
                    <label><i class="fas fa-tag"></i> نوع الاختبار</label>
                    <select id="filter-type">
                        <option value="">جميع الأنواع</option>
                        <option value="term_grade">درجة الترم</option>
                        <option value="midterm">منتصف الترم</option>
                        <option value="final">نهائي</option>
                    </select>
                </div>
                <div class="filter-group" style="display:none">
                    <label><i class="fas fa-check-circle"></i> الحالة</label>
                    <select id="filter-status">
                        <option value="">الكل</option>
                        <option value="passed">ناجح</option>
                        <option value="failed">راسب</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grades Table Section -->
        <div class="grades-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-table"></i>
                    <h4>جميع الدرجات</h4>
                </div>
                <div class="export-buttons">
                    <button class="export-btn" onclick="exportCSV()" title="تصدير CSV">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                    <button class="export-btn" onclick="exportPDF()" title="تصدير PDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    @if(isset($students) && $students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())
                        <div class="pagination-wrapper" style="margin: 24px 0; display: flex; justify-content: center;">
                            {{ $students->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @endif
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i></th>
                            <th><i class="fas fa-user"></i> الطالب</th>
                            <th><i class="fas fa-calendar"></i> الترم</th>
                            <th><i class="fas fa-comment-dots"></i> ملاحظات</th>
                            <th><i class="fas fa-file-pdf"></i> ملف الدرجات</th>
                            <th><i class="fas fa-cog"></i> إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="grades-tbody">
                        @if(!isset($students) || !is_iterable($students) || count($students) == 0)
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>لا يوجد طلاب</h5>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @php
                                $gradesByStudent = collect($grades ?? [])->keyBy('student_id');
                            @endphp
                            @foreach($students as $student)
                                @php
                                    $firstGrade = $gradesByStudent->get($student->id);
                                @endphp
                                <tr>
                                    <td><strong>{{ $loop->iteration }}</strong></td>
                                    <td>
                                        <div class="student-info">
                                            <div class="student-avatar-small">{{ mb_substr($student->full_name ?? $student->name, 0, 1) }}</div>
                                            <span class="student-name">{{ $student->full_name ?? $student->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="term-badge">{{ $firstGrade->term ?? '-' }}</span></td>
                                    <td class="notes-cell">{{ $firstGrade->notes ?? '-' }}</td>
                                    <td>
                                        @if($firstGrade && $firstGrade->pdf_path)
                                            <a href="/store/{{ ltrim(preg_replace('#^pdf_grades[\\/]#', 'pdf_grades/', $firstGrade->pdf_path), '/') }}" target="_blank" class="btn btn-sm btn-danger">
                                                <i class="fas fa-file-pdf"></i> تحميل
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                               <td>
                                    <div class="action-cell">
                                        @if($firstGrade && $firstGrade->id)
                                            <button class="btn-edit" onclick="editGrade('{{ $firstGrade->id }}')" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-delete" onclick="deleteGrade('{{ $firstGrade->id }}')" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button class="btn-edit" onclick="openAddGradeModal('{{ $student->id }}')" title="إضافة" data-student="{{ $student->id }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        <!-- Modal for Adding Grade -->
                                        <div id="addGradeModal" class="modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
                                            <div style="background:#fff;padding:32px 24px;border-radius:12px;max-width:400px;width:90%;position:relative;">
                                                <button onclick="closeAddGradeModal()" style="position:absolute;top:10px;left:10px;background:none;border:none;font-size:22px;color:#888;cursor:pointer;">&times;</button>
                                                <h4 style="margin-bottom:18px;text-align:center;color:#2d3748;font-weight:700;">رفع ملف درجات الطالب</h4>
                                                <form id="addGradeForm" action="{{ route('panel.webinars.term_grades.index') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" id="modal-student-id" name="grades[STUDENT_ID][student_id]" value="">
                                                    <div class="form-group">
                                                        <label for="modal-pdf-file"><i class="fas fa-file-pdf"></i> ملف PDF للدرجات</label>
                                                        <input type="file" id="modal-pdf-file" name="grades[STUDENT_ID][pdf_file]" accept="application/pdf" class="form-control" required>
                                                    </div>
                                                    <div class="mt-4 text-center">
                                                        <button type="submit" class="btn-save" style="width:100%;">
                                                            <i class="fas fa-save"></i>
                                                            حفظ الدرجات
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <script>
                                        function openAddGradeModal(studentId) {
                                            var modal = document.getElementById('addGradeModal');
                                            var form = document.getElementById('addGradeForm');
                                            // تحديث الحقول بالأيدي
                                            document.getElementById('modal-student-id').value = studentId;
                                            document.getElementById('modal-student-id').setAttribute('name', `grades[${studentId}][student_id]`);
                                            document.getElementById('modal-pdf-file').setAttribute('name', `grades[${studentId}][pdf_file]`);
                                            // إعادة تعيين الملف
                                            document.getElementById('modal-pdf-file').value = '';
                                            modal.style.display = 'flex';
                                        }
                                        function closeAddGradeModal() {
                                            document.getElementById('addGradeModal').style.display = 'none';
                                        }
                                        // إغلاق المودال عند الضغط خارج المحتوى
                                        window.onclick = function(event) {
                                            var modal = document.getElementById('addGradeModal');
                                            if (event.target === modal) {
                                                closeAddGradeModal();
                                            }
                                        }
                                        </script>
                                        @endif
                                    </div>
                                </td>
                                      
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts_bottom')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
        
        <script>
            (function() {
                const filterStudent = document.getElementById('filter-student');
                const filterCourse  = document.getElementById('filter-course');
                const filterTerm    = document.getElementById('filter-term');
                const filterType    = document.getElementById('filter-type');
                const filterStatus  = document.getElementById('filter-status');

                let allGrades = @json($grades ?? []);
                if (!Array.isArray(allGrades)) allGrades = Object.values(allGrades);
                let filteredGrades = [...allGrades];

                let allStudents = @json($students ?? []);
                if (!Array.isArray(allStudents)) allStudents = Object.values(allStudents);

                document.addEventListener('DOMContentLoaded', function() {
                    updateStatistics();
                });

                function updateStatistics() {
                    document.getElementById('total-students').textContent = allStudents.length;
                }

                function applyFilters() {
                    const studentSearch = filterStudent.value.toLowerCase();
                    filteredGrades = allGrades.filter(grade => {
                        return !studentSearch || (grade.student_name && grade.student_name.toLowerCase().includes(studentSearch));
                    });
                    filterTable(studentSearch);
                }

                function filterTable(search) {
                    const rows = document.querySelectorAll('#grades-tbody tr');
                    rows.forEach(row => {
                        const nameCell = row.querySelector('.student-name');
                        if (!nameCell) return;
                        const name = nameCell.textContent.toLowerCase();
                        row.style.display = !search || name.includes(search) ? '' : 'none';
                    });
                    // update count
                    const visible = [...rows].filter(r => r.style.display !== 'none').length;
                    document.getElementById('total-students').textContent = visible;
                }

                filterStudent.addEventListener('input', applyFilters);

                window.editGrade = function(gradeId) {
                    window.location.href = `/panel/grades/${gradeId}/edit`;
                };

                window.deleteGrade = function(gradeId) {
                    Swal.fire({
                        title: deleteAlertTitle,
                        text: deleteAlertHint,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: deleteAlertConfirm,
                        cancelButtonText: deleteAlertCancel
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/panel/grades/${gradeId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                credentials: 'same-origin'
                            })
                            .then(r => r.ok ? r.json() : Promise.reject(r))
                            .then(() => {
                                const btn = document.querySelector(`button[onclick=\"deleteGrade('${gradeId}')\"]`);
                                if (btn) {
                                    const row = btn.closest('tr');
                                    if (row) {
                                        // ابحث عن عمود ملف الدرجات (td الخامس)
                                        const fileTd = row.querySelectorAll('td')[4];
                                        if (fileTd) fileTd.innerHTML = '-';
                                    }
                                }
                                updateStatistics();
                                Swal.fire({
                                    icon: 'success',
                                    title: deleteAlertSuccess,
                                    text: deleteAlertSuccessHint,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            })
                            .catch(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: deleteAlertFail,
                                    text: deleteAlertFailHint,
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            });
                        }
                    });
                };

                window.refreshData = function() { location.reload(); };

                window.exportCSV = function() {
                    const rows = [...document.querySelectorAll('#grades-tbody tr')].filter(r => r.style.display !== 'none');
                    if (!rows.length) { alert('لا توجد بيانات للتصدير'); return; }
                    const headers = ['#', 'الطالب', 'الترم', 'ملاحظات'];
                    const data = rows.map((row, idx) => {
                        const cells = row.querySelectorAll('td');
                        return [
                            idx + 1,
                            cells[1]?.querySelector('.student-name')?.textContent?.trim() || '',
                            cells[2]?.textContent?.trim() || '',
                            cells[3]?.textContent?.trim() || '',
                        ];
                    });
                    const csv = [headers, ...data].map(r => r.map(c => `"${String(c).replace(/"/g,'""')}"`).join(',')).join('\n');
                    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = `grades_${new Date().toISOString().split('T')[0]}.csv`;
                    document.body.appendChild(a); a.click(); a.remove();
                };
window.addGrade = function(studentId) {
    window.location.href = `/panel/webinars/add_term_grades?student_id=${studentId}`;
};
                window.exportPDF = function() {
                    const rows = [...document.querySelectorAll('#grades-tbody tr')].filter(r => r.style.display !== 'none');
                    if (!rows.length) { alert('لا توجد بيانات للتصدير'); return; }
                    const headers = ['#', 'الطالب', 'الترم', 'ملاحظات'];
                    const body = rows.map((row, idx) => {
                        const cells = row.querySelectorAll('td');
                        return [
                            idx + 1,
                            cells[1]?.querySelector('.student-name')?.textContent?.trim() || '',
                            cells[2]?.textContent?.trim() || '',
                            cells[3]?.textContent?.trim() || '',
                        ];
                    });
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ unit: 'pt', format: 'a4', orientation: 'landscape' });
                    doc.setFontSize(16);
                    doc.text('تقرير درجات الطلاب', 40, 40);
                    doc.setFontSize(10);
                    doc.text(`التاريخ: ${new Date().toLocaleDateString('ar-SA')}`, 40, 60);
                    doc.autoTable({
                        head: [headers], body,
                        startY: 80,
                        styles: { fontSize: 9, halign: 'center' },
                        headStyles: { fillColor: [102,126,234], fontStyle: 'bold' },
                        theme: 'grid',
                        margin: { left: 20, right: 20 }
                    });
                    doc.save(`grades_report_${new Date().toISOString().split('T')[0]}.pdf`);
                };
            })();
        </script>
    @endpush
@endsection