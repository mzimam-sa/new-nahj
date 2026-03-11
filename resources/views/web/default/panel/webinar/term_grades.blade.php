@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-grades-dashboard {
            padding: 40px 20px;
            border-radius: 20px;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            color: white;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        
        .header-title h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .header-title p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
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
        
        .stat-content {
            position: relative;
            z-index: 1;
        }
        
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
        
        .stat-card.total-students .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.total-students::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.total-grades .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.total-grades::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.avg-grade .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.avg-grade::before {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.total-courses .stat-icon {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .stat-card.total-courses::before {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #718096;
            font-size: 14px;
            font-weight: 500;
        }
        
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
        
        .controls-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .controls-title i {
            font-size: 20px;
            color: #667eea;
        }
        
        .controls-title h4 {
            margin: 0;
            color: #2d3748;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
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
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .action-btn.secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
            font-size: 14px;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
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
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title i {
            font-size: 24px;
            color: #667eea;
        }
        
        .section-title h4 {
            margin: 0;
            color: #2d3748;
            font-weight: 700;
            font-size: 20px;
        }
        
        .export-buttons {
            display: flex;
            gap: 8px;
        }
        
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
        
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .grades-table {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .grades-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .grades-table thead th {
            padding: 16px 12px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            border: none;
        }
        
        .grades-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .grades-table tbody tr:hover {
            background: #f7fafc;
        }
        
        .grades-table tbody td {
            padding: 16px 12px;
            text-align: center;
            vertical-align: middle;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: right;
        }
        
        .student-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .student-name {
            font-weight: 600;
            color: #2d3748;
        }
        
        .course-name {
            font-weight: 600;
            color: #2d3748;
        }
        
        .grade-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 16px;
            min-width: 60px;
        }
        
        .grade-badge.excellent {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .grade-badge.very-good {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .grade-badge.good {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .grade-badge.pass {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .grade-badge.fail {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px; 
            border-radius: 20px;
            font-size: 13px; 
            font-weight: 600;
            
        }
        
        .status-badge.passed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .type-badge {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .term-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }
        
        .action-cell {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .btn-edit {
            background: #4299e1;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        
        .btn-edit:hover {
            background: #3182ce;
            transform: scale(1.05);
        }
        
        .btn-delete {
            background: #f56565;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        
        .btn-delete:hover {
            background: #e53e3e;
            transform: scale(1.05);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #cbd5e0;
            margin-bottom: 20px;
        }
        
        .empty-state h5 {
            color: #4a5568;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #718096;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .admin-grades-dashboard {
                padding: 20px 10px;
            }
            
            .dashboard-header {
                padding: 25px;
            }
            
            .header-content {
                flex-direction: column;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                width: 100%;
            }
            
            .action-btn {
                flex: 1;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .export-buttons {
                width: 100%;
            }
            
            .export-btn {
                flex: 1;
            }
            
            .grades-table {
                font-size: 11px;
            }
            
            .grades-table th,
            .grades-table td {
                padding: 8px 4px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admin-grades-dashboard">
      
        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card total-students">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value" id="total-students">0</div>
                    <div class="stat-label">إجمالي الطلاب</div>
                </div>
            </div>

            <div class="stat-card total-grades" style="display:none">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div class="stat-value" id="total-grades">0</div>
                    <div class="stat-label">إجمالي الدرجات</div>
                </div>
            </div>

            <div class="stat-card avg-grade" style="display:none">
                <div class="stat-content">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-value" id="avg-grade">--</div>
                    <div class="stat-label">المعدل العام</div>
                </div>
            </div>

            <div class="stat-card total-courses">
                <div class="stat-content">
                     <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-value" id="total-courses">0</div>
                    <div class="stat-label">عدد المواد</div>
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
                <div class="filter-group">
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
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="grades-table">
                    <thead> 
                        <tr>
                            <th><i class="fas fa-hashtag"></i></th>
                            <th><i class="fas fa-user"></i> الطالب</th>
                            <th><i class="fas fa-book"></i> المادة</th>
                            <th><i class="fas fa-calendar"></i> الترم</th>
                            <th><i class="fas fa-comment-dots"></i> ملاحظات</th>
                            <th><i class="fas fa-file-pdf"></i> ملف PDF</th>
                            <th><i class="fas fa-cog"></i> إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="grades-tbody">
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <h5>جارٍ تحميل البيانات...</h5>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

            {{-- @dd($grades->user->name); --}}
    @push('scripts_bottom')
        <!-- jsPDF + autoTable (for PDF export) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
        <script>
            (function() {
                const gradesTbody = document.getElementById('grades-tbody');
                const filterStudent = document.getElementById('filter-student');
                const filterCourse = document.getElementById('filter-course');
                const filterTerm = document.getElementById('filter-term');
                const filterType = document.getElementById('filter-type');
                const filterStatus = document.getElementById('filter-status');

                // Get grades passed from controller 
                let allGrades = @json($grades ?? []);
                let filteredGrades = allGrades;

                // Initialize on page load
                populateCourseFilter();
                updateStatistics();
                renderGrades();

                function populateCourseFilter() {
                    const courses = [...new Set(allGrades.map(g => g.webinar_title || 'بدون مادة'))];
                    let html = '<option value="">جميع المواد</option>';
                    courses.forEach(course => {
                        html += `<option value="${escapeHtml(course)}">${escapeHtml(course)}</option>`;
                    });
                    filterCourse.innerHTML = html;
                }

                function applyFilters() {
                    const studentSearch = filterStudent.value.toLowerCase();
                    
                    filteredGrades = allGrades.filter(grade => {
                        const studentMatch = !studentSearch || 
                            (grade.student_name && grade.student_name.toLowerCase().includes(studentSearch));
                        const courseMatch = !filterCourse.value || grade.webinar_title == filterCourse.value;
                        const termMatch = !filterTerm.value || String(grade.term) == String(filterTerm.value);
                        const typeMatch = !filterType.value || grade.type == filterType.value;
                        
                        let statusMatch = true;
                        if (filterStatus.value) {
                            const score = parseFloat(grade.score) || 0;
                            const successScore = parseFloat(grade.success_score) || 50;
                            const passed = score >= successScore;
                            statusMatch = (filterStatus.value === 'passed' && passed) || 
                                        (filterStatus.value === 'failed' && !passed);
                        }
                        
                        return studentMatch && courseMatch && termMatch && typeMatch && statusMatch;
                    });
                    
                    updateStatistics();
                    renderGrades();
                }

                function updateStatistics() {
                    const grades = filteredGrades;
                    const total = grades.length;
                    
                    // Unique students
                    const uniqueStudents = [...new Set(grades.map(g => g.student_id))].length;
                    
                    // Unique courses
                    const uniqueCourses = [...new Set(grades.map(g => g.webinar_title))].length;
                    
                    // Average grade
                    let avg = '--';
                    if (total > 0) {
                        const sum = grades.reduce((acc, g) => acc + (parseFloat(g.score) || 0), 0);
                        avg = (sum / total).toFixed(2);
                    }
                    
                    document.getElementById('total-students').textContent = uniqueStudents;
                    document.getElementById('total-grades').textContent = total;
                    document.getElementById('avg-grade').textContent = avg;
                    document.getElementById('total-courses').textContent = uniqueCourses;
                }

                function renderGrades() {
                    if (filteredGrades.length === 0) {
                        gradesTbody.innerHTML = `
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>لا توجد درجات</h5>
                                        <p>لم يتم العثور على درجات تطابق معايير البحث</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    let html = '';
                    filteredGrades.forEach((grade, idx) => {
                        const studentName = grade.student_name || 'طالب #' + grade.student_id;
                        const initial = studentName.charAt(0);
                        let pdfCell = '-';
                        if (grade.pdf_path) {
                            let pdfUrl = '/store/' + grade.pdf_path.replace(/^pdf_grades[\\\/]/, 'pdf_grades/');
                            pdfCell = `<a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> تحميل</a>`;
                        }
                        html += `
                            <tr>
                                <td><strong>${idx + 1}</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar-small">${escapeHtml(initial)}</div>
                                        <span class="student-name">${escapeHtml(studentName)}</span>
                                    </div>
                                </td>
                                <td class="course-name">${escapeHtml(grade.webinar_title || 'بدون مادة')}</td>
                                <td><span class="term-badge">${grade.term || 1}</span></td>
                                <td class="notes-cell" title="${escapeHtml(grade.notes || '-')}">${escapeHtml(grade.notes || '-')}</td>
                                <td>${pdfCell}</td>
                                <td>
                                    <div class="action-cell">
                                        <button class="btn-edit" onclick="editGrade(${grade.id})" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-delete" onclick="deleteGrade(${grade.id})" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    gradesTbody.innerHTML = html;
                }

                function getGradeClass(score) {
                    if (score >= 90) return 'excellent';
                    if (score >= 80) return 'very-good';
                    if (score >= 70) return 'good';
                    if (score >= 50) return 'pass';
                    return 'fail';
                }

                function getTypeLabel(type) {
                    const labels = {
                        'term_grade': 'درجة الترم',
                        'midterm': 'منتصف الترم',
                        'final': 'نهائي'
                    };
                    return labels[type] || type;
                }

                function escapeHtml(str) {
                    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
                        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;','=':'&#61;','/':'&#47;'}[s];
                    });
                }

                // Event listeners for filters
                filterStudent.addEventListener('input', applyFilters);
                filterCourse.addEventListener('change', applyFilters);
                filterTerm.addEventListener('change', applyFilters);
                filterType.addEventListener('change', applyFilters);
                filterStatus.addEventListener('change', applyFilters);

                // Edit grade function
                window.editGrade = function(gradeId) {
                    // Redirect to edit page or open modal
                    window.location.href = `/panel/grades/${gradeId}/edit`;
                };

                // Delete grade function
                window.deleteGrade = function(gradeId) {
                    if (!confirm('هل أنت متأكد من حذف هذه الدرجة؟')) {
                        return;
                    }
                    
                    // Send delete request
                    fetch(`/panel/grades/${gradeId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    })
                    .then(r => r.ok ? r.json() : Promise.reject(r))
                    .then(data => {
                        // Remove from array and re-render
                        allGrades = allGrades.filter(g => g.id != gradeId);
                        applyFilters();
                        alert('تم حذف الدرجة بنجاح');
                    })
                    .catch(() => {
                        alert('فشل في حذف الدرجة');
                    });
                };

                // Refresh data function
                window.refreshData = function() {
                    location.reload();
                };

                // Export CSV
                window.exportCSV = function() {
                    if (!filteredGrades || filteredGrades.length === 0) {
                        alert('لا توجد بيانات للتصدير');
                        return;
                    }
                    
                    const headers = ['#','الطالب','المادة','الدرجة','درجة النجاح','النوع','الترم','الحالة'];
                    const rows = filteredGrades.map((g, idx) => {
                        const score = parseFloat(g.score) || 0;
                        const success = parseFloat(g.success_score) || 0;
                        const status = score >= success ? 'ناجح' : 'راسب';
                        return [
                            idx + 1,
                            g.student_name || '',
                            g.webinar_title || '',
                            g.score || '',
                            g.success_score || '',
                            g.type || '',
                            g.term || '',
                            status
                        ];
                    });
                    
                    const csvContent = [headers, ...rows]
                        .map(r => r.map(cell => `"${String(cell).replace(/"/g,'""')}"`).join(','))
                        .join('\n');
                    
                    const BOM = '\uFEFF'; // UTF-8 BOM for Arabic support
                    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `grades_${new Date().toISOString().split('T')[0]}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                };

                // Export PDF using jsPDF + autoTable
                window.exportPDF = function() {
                    if (!filteredGrades || filteredGrades.length === 0) {
                        alert('لا توجد بيانات للتصدير');
                        return;
                    }
                    
                    const headers = ['#','الطالب','المادة','الدرجة','درجة النجاح','النوع','الترم','الحالة'];
                    const body = filteredGrades.map((g, idx) => {
                        const score = parseFloat(g.score) || 0;
                        const success = parseFloat(g.success_score) || 0;
                        const status = score >= success ? 'ناجح' : 'راسب';
                        return [
                            idx + 1,
                            g.student_name || '',
                            g.webinar_title || '',
                            g.score || '',
                            g.success_score || '',
                            g.type || '',
                            g.term || '',
                            status
                        ];
                    });

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({ 
                        unit: 'pt', 
                        format: 'a4', 
                        orientation: 'landscape' 
                    });
                    
                    doc.setFontSize(16);
                    doc.text('تقرير درجات الطلاب', 40, 40);
                    
                    doc.setFontSize(10);
                    doc.text(`التاريخ: ${new Date().toLocaleDateString('ar-SA')}`, 40, 60);
                    doc.text(`إجمالي الدرجات: ${filteredGrades.length}`, 40, 75);
                    
                    doc.autoTable({
                        head: [headers],
                        body: body,
                        startY: 90,
                        styles: { 
                            fontSize: 9,
                            halign: 'center'
                        },
                        headStyles: { 
                            fillColor: [102,126,234],
                            fontStyle: 'bold'
                        },
                        theme: 'grid',
                        margin: { left: 20, right: 20 }
                    });
                    
                    doc.save(`grades_report_${new Date().toISOString().split('T')[0]}.pdf`);
                };

            })();
        </script>
    @endpush
@endsection