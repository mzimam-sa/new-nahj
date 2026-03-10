{{-- @extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush
 
@section('content')
<section class="panel-section-card py-20 px-25 mt-20">
    <h2 class="section-title">درجاتي</h2>

    @if (isset($grades) && count($grades)) 
        <div class="text-muted">إجمالي الدرجات المسجلة: {{ count($grades) }}</div>
        
    @else
  <div class="table-responsive mt-20">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الفصل / الكورس</th>
                    <th>الترم</th>
                    <th>النوع</th>
                    <th>الدرجة</th>
                    <th>درجة النجاح</th>
                    <th>ملاحظات</th>
                    <th>تاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grades as $i => $g)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional($g->webinar)->title ?? '-' }}</td>
                        <td>{{ $g->term ?? '-' }}</td>
                        <td>{{ $g->type ?? '-' }}</td>
                        <td>{{ $g->score !== null ? number_format($g->score, 2) : '-' }}</td>
                        <td>{{ $g->success_score !== null ? number_format($g->success_score, 2) : '-' }}</td>
                        <td>{{ $g->notes ?? '-' }}</td>
                        <td>{{ $g->created_at ? $g->created_at->format('Y-m-d') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">لا توجد درجات حتى الآن</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

  
</section>
@endsection --}}



@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .grades-dashboard {
            /* background: linear-gradient(135deg, #0000009a 0%, #3b0aad71 100%); */
            padding: 40px 20px;
            border-radius: 20px;
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .student-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .student-details h2 {
            margin: 0 0 5px 0;
            color: #2d3748;
            font-size: 24px;
        }
        
        .student-details p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }
        
        .stat-card.average .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.passed .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.failed .stat-icon {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
        
        .filters-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        
        .filters-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .filters-header i {
            font-size: 20px;
            color: #667eea;
        }
        
        .filters-header h4 {
            margin: 0;
            color: #2d3748;
            font-weight: 600;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
            font-size: 14px;
        }
        
        .filter-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-group select:focus {
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
        
        .course-name {
            font-weight: 600;
            color: #2d3748;
            text-align: right;
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
        
        .notes-cell {
            max-width: 200px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            color: #718096;
            font-size: 13px;
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
        
        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        @media (max-width: 768px) {
            .grades-dashboard {
                padding: 20px 10px;
            }
            
            .dashboard-header {
                padding: 20px;
            }
            
            .header-info {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .grades-table {
                font-size: 12px;
            }
            
            .grades-table th,
            .grades-table td {
                padding: 10px 6px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="grades-dashboard">
      
        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card average">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value" id="avg-grade">--</div>
                <div class="stat-label">المعدل العام</div>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="avg-progress" style="width: 0%"></div>
                </div>
            </div>

            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="stat-value" id="total-grades">0</div>
                <div class="stat-label">إجمالي المواد</div>
            </div>

            <div class="stat-card passed">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value" id="passed-count">0</div>
                <div class="stat-label">المواد الناجحة</div>
            </div>

            <div class="stat-card failed">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value" id="failed-count">0</div>
                <div class="stat-label">المواد الراسبة</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                <i class="fas fa-filter"></i>
                <h4>تصفية النتائج</h4>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> الترم</label>
                    <select id="filter-term">
                        <option value="">جميع الترمات</option>
                        <option value="1">الترم الأول</option>
                        <option value="2">الترم الثاني</option>
                        <option value="3">الترم الثالث</option>
                        <option value="4">الترم الرابع</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> نوع الاختبار</label>
                    <select id="filter-type">
                        <option value="">جميع الأنواع</option>
                        <option value="term_grade">درجة الترم</option>
                        <option value="midterm">منتصف الترم</option>
                        <option value="final">نهائي</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-book"></i> المادة</label>
                    <select id="filter-course">
                        <option value="">جميع المواد</option>
                        <!-- Will be populated dynamically -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Grades Table Section -->
        <div class="grades-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>درجاتي</h4>
                </div>

                <div style="display:flex;gap:8px;align-items:center;">
                   
                </div>
            </div> 

            <div class="table-responsive">
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i></th>
                            <th><i class="fas fa-book"></i> المادة</th>
                            <th><i class="fas fa-calendar"></i> الترم</th>
                            <th><i class="fas fa-comment-dots"></i> ملاحظات</th>
                            <th><i class="fas fa-file-pdf"></i> سجل الدرجات</th>
                        </tr>
                    </thead>
                    <tbody id="grades-tbody">
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <h5>جارٍ تحميل الدرجات...</h5>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts_bottom')
        <!-- jsPDF + autoTable (for PDF export) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
        <script>
            (function() {
                const gradesTbody = document.getElementById('grades-tbody');
                const filterTerm = document.getElementById('filter-term');
                const filterType = document.getElementById('filter-type');
                const filterCourse = document.getElementById('filter-course');

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
                    filteredGrades = allGrades.filter(grade => {
                        const termMatch = !filterTerm.value || String(grade.term) == String(filterTerm.value);
                        const typeMatch = !filterType.value || grade.type == filterType.value;
                        const courseMatch = !filterCourse.value || grade.webinar_title == filterCourse.value;
                        return termMatch && typeMatch && courseMatch;
                    });
                    updateStatistics();
                    renderGrades();
                }

                function updateStatistics() {
                    const grades = filteredGrades;
                    const total = grades.length;
                    if (total === 0) {
                        document.getElementById('avg-grade').textContent = '--';
                        document.getElementById('avg-progress').style.width = '0%';
                        document.getElementById('total-grades').textContent = '0';
                        document.getElementById('passed-count').textContent = '0';
                        document.getElementById('failed-count').textContent = '0';
                        return;
                    }
                    const sum = grades.reduce((acc, g) => acc + (parseFloat(g.score) || 0), 0);
                    const avg = (sum / total).toFixed(2);
                    const passed = grades.filter(g => parseFloat(g.score) >= parseFloat(g.success_score || 50)).length;
                    const failed = total - passed;
                    document.getElementById('avg-grade').textContent = avg;
                    document.getElementById('avg-progress').style.width = Math.min(100, avg) + '%';
                    document.getElementById('total-grades').textContent = total;
                    document.getElementById('passed-count').textContent = passed;
                    document.getElementById('failed-count').textContent = failed;
                }

                function renderGrades() {
                    if (filteredGrades.length === 0) {
                        gradesTbody.innerHTML = `
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>لا توجد درجات</h5>
                                        <p>لم يتم تسجيل أي درجات بعد</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    let html = '';
                    filteredGrades.forEach((grade, idx) => {
                        let pdfCell = '-';
                        if (grade.pdf_path) {
                            let pdfUrl = '/store/' + grade.pdf_path.replace(/^pdf_grades[\\\/]/, 'pdf_grades/');
                            pdfCell = `<a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> تحميل</a>`;
                        }
                        html += `
                            <tr>
                                <td><strong>${idx + 1}</strong></td>
                                <td class="course-name">${escapeHtml(grade.webinar_title || 'بدون مادة')}</td>
                                <td><span class="term-badge">${grade.term || 1}</span></td>
                                <td class="notes-cell" title="${escapeHtml(grade.notes || '-')}">${escapeHtml(grade.notes || '-')}</td>
                                <td>${pdfCell}</td>
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
                filterTerm.addEventListener('change', applyFilters);
                filterType.addEventListener('change', applyFilters);
                filterCourse.addEventListener('change', applyFilters);

             

            })();
        </script>
     @endpush
@endsection