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
use Illuminate\Support\Facades\Storage;

class termGradesController extends Controller
{
    // صفحة إضافة درجات الترم
    public function termGradesShowCreate()
    {
        // يمكنك تخصيص البيانات حسب الحاجة
        return view(getTemplate() . '.panel.webinar.add_term_grades');
    }

   public function termGrades(Request $request)
    {
        $studentIds = \App\Models\Sale::whereNotNull('webinar_id')
            ->whereNull('refund_at')
            ->pluck('buyer_id')->unique()->toArray();

        $students = \App\User::whereIn('id', $studentIds)->paginate(15);

        // درجة وحدة لكل طالب (آخر درجة)
        $grades = WebinarGrade::whereIn('student_id', $studentIds)
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

        return view(getTemplate() . '.panel.webinar.term_grades_supervisor', [
            'grades'      => $allGrades,
            'students'    => $students,
            'adminPrefix' => getAdminPanelUrlPrefix(),
        ]);
    }

    private function getRedirectRoute(): string
    {
        $adminPrefix = getAdminPanelUrlPrefix();
        
        // إذا الطلب جاي من مسار الأدمن
        if (request()->is($adminPrefix . '/*') || request()->is($adminPrefix)) {
            return route('admin.webinars.term_grades');
        }
        
        // إذا المستخدم supervisor حتى لو الطلب من مكان ثاني
        if (auth()->user()->isSupervisor()) {
            return route('admin.webinars.term_grades');
        }
        
        return route('panel.webinars.term_grades');
    }


    public function store(Request $request, $webinarId)
    {
        $data = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:users,id',
            'grades.*.pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        foreach ($data['grades'] as $studentId => $gradeData) {
            $file = $request->grades[$studentId]['pdf_file'] ?? null;
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileName = 'grade_' . $webinarId . '_' . $studentId . '_' . time() . '.pdf';
                $path = $file->storeAs('pdf_grades', $fileName, 'public');
                WebinarGrade::updateOrCreate(
                    [
                        'webinar_id' => $webinarId,
                        'student_id' => $studentId,
                    ],
                    [
                        'pdf_path' => $path,
                        'creator_id' => auth()->id(),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'تم رفع ملف درجات الطالب بنجاح');
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

        // return redirect()->route('panel.webinars.term_grades.index')->with('success', 'تم رفع ملف درجات الطالب بنجاح');
        return redirect($this->getRedirectRoute())
    ->with('success', 'تم رفع ملف درجات الطالب بنجاح');
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

    $adminPrefix = getAdminPanelUrlPrefix();
    $isSupervisor = auth()->user()->isSupervisor();

    $backUrl     = $isSupervisor ? url($adminPrefix . '/webinars/term_grades')     : route('panel.webinars.term_grades');
    $formAction  = $isSupervisor ? url($adminPrefix . '/webinars/grades/' . $id)   : url('/panel/grades/' . $id);
    $cancelUrl   = $isSupervisor ? url($adminPrefix . '/webinars/term_grades')      : url('/panel/webinars/term_grades');

    return view(getTemplate() . '.panel.webinar.edit_grade', compact(
        'grade', 'backUrl', 'formAction', 'cancelUrl'
    ));
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
        if ($grade->pdf_path && Storage::disk('public')->exists($grade->pdf_path)) {
            Storage::disk('public')->delete($grade->pdf_path);
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

    // return redirect()->route('panel.webinars.term_grades.index')->with('success', 'تم تحديث الدرجة');
    return redirect($this->getRedirectRoute())->with('success', 'تم تحديث الدرجة');
}

    // delete grade (DELETE) — already returns json, keep it
    public function deleteGrade($id)
    {
        $grade = WebinarGrade::findOrFail($id);
        $grade->delete();

        return response()->json(['status' => 'ok']);
    }

    // teacherGrades method (if not present) - reuse termGrades logic or add dedicated view
    // public function teacherGrades()
    // {
    //     $user = auth()->user();

    //     // جلب جميع الطلاب الذين لديهم درجات عند هذا المدرس في أي دورة
    //     $grades = WebinarGrade::whereHas('webinar', function ($q) use ($user) {
    //             $q->where('creator_id', $user->id);
    //         })
    //         ->with(['webinar', 'student'])
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     $grades->each(function($g) {
    //         $g->student_name = optional($g->student)->full_name ?? optional($g->student)->name;
    //         $g->student_id = $g->student_id;
    //         $g->webinar_title = optional($g->webinar)->title;
    //     });

    //     // جلب جميع الطلاب الذين اشتروا أي دورة على المنصة
    //     $studentIds = \App\Models\Sale::whereNotNull('webinar_id')
    //         ->whereNull('refund_at')
    //         ->pluck('buyer_id')->unique()->toArray();
    //     $students = \App\User::whereIn('id', $studentIds)->get();

    //     // تجهيز مصفوفة grades بحيث تحتوي على كل طالب حتى لو لم يكن لديه درجة
    //     $gradesByStudent = $grades->keyBy('student_id');
    //     $allGrades = collect();
    //     foreach ($students as $student) {
    //         $grade = $gradesByStudent->get($student->id);
    //         $allGrades->push((object) [
    //             'id' => $grade->id ?? null,
    //             'student_id' => $student->id,
    //             'student_name' => $student->full_name ?? $student->name,
    //             'webinar_title' => $grade->webinar_title ?? null,
    //             'score' => $grade->score ?? null,
    //             'success_score' => $grade->success_score ?? null,
    //             'type' => $grade->type ?? null,
    //             'term' => $grade->term ?? null,
    //             'notes' => $grade->notes ?? null,
    //             'pdf_path' => $grade->pdf_path ?? null,
    //         ]);
    //     }

    //     return view(getTemplate() . '.panel.webinar.term_grades', ['grades' => $allGrades, 'students' => $students]);
    // }
    public function teacherGrades()
{
    $studentIds = \App\Models\Sale::whereNotNull('webinar_id')
        ->whereNull('refund_at')
        ->pluck('buyer_id')->unique()->toArray();

    $students = \App\User::whereIn('id', $studentIds)->get();

    $grades = WebinarGrade::whereIn('student_id', $studentIds)
        ->with('webinar')
        ->get()
        ->keyBy('student_id'); // طالب → درجة وحدة

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
}



