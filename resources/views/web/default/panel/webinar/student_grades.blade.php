@extends(getTemplate() . '.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .grades-dashboard {
            padding: 40px 20px;
            border-radius: 20px;
            min-height: 100vh;
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
        .section-title i { font-size: 24px; color: #667eea; }
        .section-title h4 { margin: 0; color: #2d3748; font-weight: 700; font-size: 20px; }
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
        .grades-table tbody tr:hover { background: #f7fafc; }
        .grades-table tbody td {
            padding: 16px 12px;
            text-align: center;
            vertical-align: middle;
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
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 64px; color: #cbd5e0; margin-bottom: 20px; }
        .empty-state h5 { color: #4a5568; margin-bottom: 10px; }
        .empty-state p { color: #718096; font-size: 14px; }
        @media (max-width: 768px) {
            .grades-dashboard { padding: 20px 10px; }
            .section-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .grades-table { font-size: 12px; }
            .grades-table th, .grades-table td { padding: 10px 6px; }
        }
    </style>
@endpush

@section('content')
    <div class="grades-dashboard">
        <div class="grades-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>درجاتي</h4>
                </div>
            </div>

            <div class="table-responsive">
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i></th>
                            <th><i class="fas fa-calendar"></i> الترم</th>
                            <th><i class="fas fa-comment-dots"></i> ملاحظات</th>
                            <th><i class="fas fa-file-pdf"></i> سجل الدرجات</th>
                        </tr>
                    </thead>
                    <tbody id="grades-tbody">
                        <tr>
                            <td colspan="4">
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
        <script>
            (function() {
                const gradesTbody = document.getElementById('grades-tbody');

                let allGrades = @json($grades ?? []);

                renderGrades();

                function renderGrades() {
                    if (!allGrades || allGrades.length === 0) {
                        gradesTbody.innerHTML = `
                            <tr>
                                <td colspan="4">
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
                    allGrades.forEach((grade, idx) => {
                        let pdfCell = '-';
                        if (grade.pdf_path) {
                            let pdfUrl = '/store/' + grade.pdf_path.replace(/^pdf_grades[\\\/]/, 'pdf_grades/');
                            pdfCell = `<a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> تحميل</a>`;
                        }
                        html += `
                            <tr>
                                <td><strong>${idx + 1}</strong></td>
                                <td><span class="term-badge">${grade.term || '-'}</span></td>
                                <td class="notes-cell" title="${escapeHtml(grade.notes || '-')}">${escapeHtml(grade.notes || '-')}</td>
                                <td>${pdfCell}</td>
                            </tr>
                        `;
                    });
                    gradesTbody.innerHTML = html;
                }

                function escapeHtml(str) {
                    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
                        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;','=':'&#61;','/':'&#47;'}[s];
                    });
                }
            })();
        </script>
    @endpush
@endsection