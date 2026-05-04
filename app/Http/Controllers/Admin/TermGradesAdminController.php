<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermGradesAdminController extends Controller  
{
    public function index(Request $request)
    {
        // إجبار اللغة على العربية لتفعيل RTL في الأدمن
        app()->setLocale('ar');
        // نفس منطق Panel\TermGradesController@termGrades
        $studentIds = \App\Models\Sale::whereNotNull('webinar_id')
            ->whereNull('refund_at')
            ->pluck('buyer_id')->unique()->toArray();

        $students = \App\User::whereIn('id', $studentIds)->paginate(15);

        $grades = \App\Models\WebinarGrade::whereIn('student_id', $studentIds)
            ->with('webinar')
            ->get()
            ->keyBy('student_id');

        $allGrades = collect();
        foreach ($students as $student) {
            $grade = $grades->get($student->id);
            $allGrades->push((object) [
                'id'            => $grade->id ?? null,
                'student_id'    => $student->id,
                'student_name'  => $student->full_name ?? $student->name,
                'webinar_title' => $grade ? optional($grade->webinar)->title : null,
                'webinar_id'    => $grade->webinar_id ?? null,
                'score'         => $grade->score ?? null,
                'success_score' => $grade->success_score ?? null,
                'type'          => $grade->type ?? null,
                'term'          => $grade->term ?? null,
                'notes'         => $grade->notes ?? null,
                'pdf_path'      => $grade->pdf_path ?? null,
            ]);
        }

        return view(getTemplate() . '.panel.webinar.term_grades', [
            'grades'   => $allGrades,
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:users,id',
            'grades.*.pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $grades = $data['grades'] ?? [];

        foreach ($grades as $studentId => $g) {
            $file = $request->grades[$studentId]['pdf_file'] ?? null;
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileName = 'grade_' . $studentId . '_' . time() . '.pdf';
                $path = $file->storeAs('pdf_grades', $fileName, 'public');
                \App\Models\WebinarGrade::create([
                    'student_id' => $g['student_id'],
                    'pdf_path' => $path,
                    'creator_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('admin.term_grades.index')->with('success', 'تم رفع ملف درجات الطالب بنجاح');
    }
    
}
