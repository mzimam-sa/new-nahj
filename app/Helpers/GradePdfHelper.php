<?php
namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\WebinarGrade;

class GradePdfHelper
{
    /**
     * توليد ملف PDF للدرجات وتخزينه
     */
    public static function generateStudentGradePdf(WebinarGrade $grade)
    {
        $data = [
            'student_name' => optional($grade->student)->full_name ?? optional($grade->student)->name,
            'webinar_title' => optional($grade->webinar)->title,
            'score' => $grade->score,
            'success_score' => $grade->success_score,
            'term' => $grade->term,
            'type' => $grade->type,
            'notes' => $grade->notes,
        ];

        $pdf = Pdf::loadView('pdf.student_grade', $data);
        $pdfPath = 'pdf_grades/' . $grade->student_id . '_' . $grade->webinar_id . '.pdf';
        Storage::disk('local')->put($pdfPath, $pdf->output());
        return $pdfPath;
    }
}
