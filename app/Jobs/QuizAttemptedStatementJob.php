<?php

namespace App\Jobs;

use App\Services\NelcService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class QuizAttemptedStatementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // public $tries = 3;        
    // public $backoff = 10;     

    protected $student;
    protected $course;
    protected $quizResult;
    protected $type;

    public function __construct($type,$student, $course,$quizResult)
    {
        $this->student = $student;
        $this->course = $course;
        $this->quizResult = $quizResult;
        $this->type = $type;
    }

    public function handle(NelcService $nelcService)
    {
        if (!$this->course || !$this->student) {
            Log::warning('NELC: missing course or student');
            return;
        }

        // ✅ اضبط الـ locale قبل أي شي
        app()->setLocale('ar');

        // ✅ احمل العلاقات
        $this->course->loadMissing('teacher');
        $this->assignment->loadMissing('chapter');
        
        try {
            $response = $nelcService->sendStatement($this->type, $this->student, $this->course,$this->quizResult);

            Log::info('NELC xAPI statement sent', [
                'type' => $this->type,
                'student_id' => $this->student->id,
                'course_id' => $this->course->id,
                'quiz_result_id' => $this->quizResult->id,
                'status' => $response['status'] ?? null,
                'uuid' => $response['body'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('NELC xAPI statement failed', [
                'type' => $this->type,
                'student_id' => $this->student->id,
                'course_id' => $this->course->id,
                'quiz_id' => $this->quiz->id,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}