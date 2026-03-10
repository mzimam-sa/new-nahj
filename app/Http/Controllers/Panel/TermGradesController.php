<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller; 

use App\Models\Webinar;
use App\User;
use App\Models\WebinarGrade;
use App\Models\Quiz; 
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class termGradesController extends Controller
{
    public function termGrades(Request $request)
    {
        $user = auth()->user();

        if (!$user->isTeacher() || !$user->isOrganization()) {
            $grades = WebinarGrade::whereHas('webinar', function ($q) use ($user) {
                $q->where('creator_id', $user->id);
            })
            ->with(['webinar', 'student'])
            ->orderBy('created_at', 'desc')
            ->get();

            // add helper attributes used by JS / views
            $grades->each(function($g) {
                $g->student_name = optional($g->student)->full_name ?? optional($g->student)->name;
                $g->student_id = $g->student_id;
                $g->webinar_title = optional($g->webinar)->title;
            });

            $webinars = Webinar::where('creator_id', $user->id)->get();
            $quizzes = Quiz::where('creator_id', $user->id)->get();
        }

        return view(getTemplate() . '.panel.webinar.term_grades', compact('webinars', 'quizzes', 'grades'));
    }


public function store(Request $request, $webinarId)
{
    $data = $request->validate([
        'grades' => 'required|array',
        'grades.*.score' => 'nullable|numeric',
        'grades.*.term' => 'nullable|integer',
        'grades.*.type' => 'nullable|string',
    ]);

    foreach ($data['grades'] as $studentId => $gradeData) {

        
        WebinarGrade::updateOrCreate(
            [
                'webinar_id' => $webinarId,
                'student_id' => $studentId,
                'term' => $gradeData['term'] ?? null,
                'type' => $gradeData['type'] ?? null,
            ],
            [
                'score' => $gradeData['score'],
                'success_score' => $gradeData['success_score'] ?? null,
                'notes' => $gradeData['notes'] ?? null,
                'creator_id' => auth()->id(),
            ]
        );
    }

    return redirect()->back()->with('success', 'تم حفظ الدرجات');
}


        public function termGradesShowCreate()
        {
              $user = auth()->user();

        if (!$user->isTeacher() || !$user->isOrganization()) {
                
                $webinars = Webinar::where('creator_id', $user->id)->get();
                $quizzes = Quiz::where('creator_id', $user->id)->get();

        }

        // try to get students from sales for this teacher, fallback to all students
        $sales = Sale::where('seller_id', $user->id)
            ->whereNull('refund_at')
            ->get();

        $studentIds = $sales->pluck('buyer_id')->unique()->toArray();

        if (!empty($studentIds)) {
            $students = User::whereIn('id', $studentIds)->get();
        } else {
            // fallback: all users with student role (adjust role field if different)
            $students = User::where('role_name', 'student')->get();
        } 

        return view(getTemplate() . '.panel.webinar.add_term_grades', compact('webinars', 'quizzes', 'students'));
    }


      public function studentsForWebinar($webinarId): JsonResponse
    {
        $webinar = Webinar::find($webinarId);

        if (!$webinar) {
            return response()->json([]);
        }


        // جلب الطلاب الذين اشتروا الفصل بشكل صريح
        $sales = $webinar->sales()->with('buyer')->get();
        $students = $sales->pluck('buyer')->filter()->unique('id')->values();

        // جلب درجات الطلاب لهذا الفصل
        $grades = WebinarGrade::where('webinar_id', $webinarId)->get()->keyBy('student_id');


        $payload = $students->map(function ($s) use ($grades) {
            $grade = isset($grades[$s->id]) ? $grades[$s->id] : null;
            return [
                'id' => $s->id,
                'name' => $s->full_name ?? $s->name,
                'score' => $grade ? $grade->score : '',
                'type' => $grade ? $grade->type : 'term_grade',
                'term' => $grade ? $grade->term : 1,
                'success_score' => $grade ? $grade->success_score : '',
                'notes' => $grade ? $grade->notes : '',
                'pdf_path' => $grade ? $grade->pdf_path : null,
            ];
        });

        return response()->json($payload);
    }



    public function termGradesStore(Request $request)
    {
        $data = $request->validate([
            'webinar_id' => 'nullable|exists:webinars,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:users,id',
            'grades.*.enabled' => 'nullable|in:1',
            'grades.*.score' => 'nullable|numeric',
            'grades.*.success_score' => 'nullable|numeric',
            'grades.*.term' => 'nullable|integer',
            'grades.*.type' => 'nullable|string|max:50',
            'grades.*.notes' => 'nullable|string|max:1000',
            'grades.*.pdf_file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $webinarId = $data['webinar_id'] ?? null;
        $grades = $data['grades'] ?? [];

        foreach ($grades as $studentId => $g) {
            $enabled = isset($g['enabled']) && $g['enabled'] == 1;
            $hasScore = isset($g['score']) && $g['score'] !== '';
            if (! $enabled && ! $hasScore) {
                continue;
            }

            $term = $g['term'] ?? 1;
            $type = $g['type'] ?? 'term_grade';

            $updateData = [
                'score' => $g['score'] ?? null,
                'success_score' => $g['success_score'] ?? null,
                'notes' => $g['notes'] ?? null,
                'creator_id' => auth()->id(),
            ];

            // جلب السجل الحالي إن وجد
            $gradeRow = \App\Models\WebinarGrade::where('webinar_id', $webinarId)
                ->where('student_id', $g['student_id'])
                ->where('term', $term)
                ->where('type', $type)
                ->first();

            $file = $request->grades[$studentId]['pdf_file'] ?? null;
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileName = 'grade_' . $webinarId . '_' . $studentId . '_' . time() . '.pdf';
                $path = $file->storeAs('pdf_grades', $fileName, 'public');
                $updateData['pdf_path'] = $path;
            } elseif (! $gradeRow || !$gradeRow->pdf_path) {
                // إذا لم يكن هناك ملف سابق يجب رفع ملف جديد
                return redirect()->back()->withErrors(['grades.' . $studentId . '.pdf_file' => 'رفع ملف PDF إجباري لكل طالب']);
            }

            \App\Models\WebinarGrade::updateOrCreate(
                [
                    'webinar_id' => $webinarId,
                    'student_id' => $g['student_id'],
                    'term' => $term,
                    'type' => $type,
                ],
                $updateData
            );
        }

        return redirect()->back()->with('success', 'تم حفظ الدرجات');
                            // ...existing code...
    }


    public function termGradesShow($id)
    {
        $termGrade = TermGrade::findOrFail($id);

        return view(getTemplate() . '.panel.webinar.show_term_grades', compact('termGrade'));
    }

    public function termGradesList(Request $request)
    {
        $user = auth()->user();

        if (!$user->isTeacher() || !$user->isOrganization()) {
                    
                    $webinars = Webinar::where('creator_id', $user->id)->get();

                      $quizzes = Quiz::where('creator_id', $user->id)->get();

        } 

        return view(getTemplate() . '.panel.webinar.list_term_grades', compact('webinars', 'quizzes')); 
    }

 

  
    public function termGradesStatistics(Request $request)
    {
        $user = auth()->user();

        if (!$user->isTeacher() || !$user->isOrganization()) {
                    
                    $webinars = Webinar::where('creator_id', $user->id)->get();

                      $quizzes = Quiz::where('creator_id', $user->id)->get();

        }

        return view(getTemplate() . '.panel.webinar.term_grades_statistics', compact('webinars', 'quizzes')); 
    }

    public function studentGrades()
    {
 


        $user = auth()->user();

        $grades = WebinarGrade::where('student_id', $user->id)
            ->with('webinar')
            ->orderBy('created_at', 'desc')
            ->get();  
 
        $grades->each(function($g) {
            $g->student_name = optional($g->student)->full_name ?? optional($g->student)->name;
            $g->webinar_title = optional($g->webinar)->title;
            $g->student_id = $g->student_id;
             
        });



         

        return view(getTemplate() . '.panel.webinar.student_grades', compact('grades'));
    }
  

    // show edit form OR return JSON for AJAX requests
    public function editGrade(Request $request, $id)
    {
        $grade = WebinarGrade::with(['student', 'webinar'])->findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) { 
            return response()->json($grade);
        }
        // @dd($grade);

        return view(getTemplate() . '.panel.webinar.edit_grade', compact('grade'));
    }

    // update grade (PATCH) — respond JSON for AJAX 
   public function updateGrade(Request $request, $id)
{
    $grade = WebinarGrade::findOrFail($id);

    $request->validate([
        'term'     => 'nullable|integer',
        'notes'    => 'nullable|string|max:2000',
        'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
    ]);

    $updateData = [
        'term'  => $request->input('term', $grade->term),
        'notes' => $request->input('notes', $grade->notes),
    ];

    $file = $request->file('pdf_file');

    if ($file && $file->isValid()) {
        // حذف الملف القديم
        if ($grade->pdf_path && \Storage::disk('public')->exists($grade->pdf_path)) {
            \Storage::disk('public')->delete($grade->pdf_path);
        }
        $fileName = 'grade_' . $grade->webinar_id . '_' . $grade->student_id . '_' . time() . '.pdf';
        $path = $file->storeAs('pdf_grades', $fileName, 'public');
        $updateData['pdf_path'] = $path;
    } elseif (!$grade->pdf_path) {
        return redirect()->back()->withErrors(['pdf_file' => 'رفع ملف PDF إجباري']);
    }

    $grade->update($updateData);

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['status' => 'ok', 'grade' => $grade->fresh()]);
    }

    return redirect()->route('panel.webinars.term_grades.index')->with('success', 'تم تحديث الدرجة');
}

    // delete grade (DELETE) — already returns json, keep it
    public function deleteGrade($id)
    {
        $grade = WebinarGrade::findOrFail($id);
        $grade->delete();

        return response()->json(['status' => 'ok']);
    }

    // teacherGrades method (if not present) - reuse termGrades logic or add dedicated view
    public function teacherGrades()
    {
        $user = auth()->user();

        $grades = WebinarGrade::whereHas('webinar', function ($q) use ($user) {
                $q->where('creator_id', $user->id);
            })
            ->with(['webinar', 'student'])
            ->orderBy('created_at', 'desc')
            ->get();

        $grades->each(function($g) {
            $g->student_name = optional($g->student)->full_name ?? optional($g->student)->name;
            $g->student_id = $g->student_id;
            $g->webinar_title = optional($g->webinar)->title;
        });

        return view(getTemplate() . '.panel.webinar.term_grades', compact('grades'));
    }
}



