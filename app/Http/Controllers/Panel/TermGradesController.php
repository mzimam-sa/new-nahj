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

        // Prefer explicit relation first
        if (method_exists($webinar, 'students')) {
            $students = $webinar->students()->select('id', 'full_name', 'name')->get();
        } elseif (method_exists($webinar, 'sales')) {
            $sales = $webinar->sales()->whereNull('refund_at')->with('buyer')->get();
            $students = $sales->pluck('buyer')->filter()->unique('id')->values();
        } else {
            $students = User::where('role', 'student')->select('id', 'full_name', 'name')->get();
        }

        $payload = $students->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->full_name ?? $s->name,
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
        ]);


        $webinarId = $data['webinar_id'] ?? null;
        $grades = $data['grades'] ?? [];

        foreach ($grades as $studentId => $g) {
            // skip rows not enabled or with no meaningful value
            $enabled = isset($g['enabled']) && $g['enabled'] == 1;
            $hasScore = isset($g['score']) && $g['score'] !== '';
            if (! $enabled && ! $hasScore) {
                continue;
            }

            // Normalize fields
            $term = $g['term'] ?? 1;
            $type = $g['type'] ?? 'term_grade';

            WebinarGrade::updateOrCreate(
                [
                    'webinar_id' => $webinarId,
                    'student_id' => $g['student_id'],
                    'term' => $term,
                    'type' => $type,
                ],
                [
                    'score' => $g['score'] ?? null,
                    'success_score' => $g['success_score'] ?? null,
                    'notes' => $g['notes'] ?? null,
                    'creator_id' => auth()->id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'تم حفظ الدرجات');
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

        $data = $request->validate([
            'score' => 'nullable|numeric',
            'success_score' => 'nullable|numeric',
            'type' => 'nullable|string|max:50', 
            'term' => 'nullable|integer',
            'notes' => 'nullable|string|max:2000',
        ]);

        $grade->update($data);  

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'ok', 'grade' => $grade]);
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



