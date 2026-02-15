<?php

namespace App\Jobs;

use App\Services\NelcService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNelcStatementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;        
    public $backoff = 10;     

    protected $student;
    protected $course;
    protected $type;

    public function __construct($student, $course, $type)
    {
        $this->student = $student;
        $this->course = $course;
        $this->type = $type;
    }

    public function handle(NelcService $nelcService)
    {
        try {
            $response = $nelcService->sendStatement($this->type, $this->student, $this->course);

            Log::info('NELC xAPI statement sent', [
                'type' => $this->type,
                'student_id' => $this->student->id,
                'course_id' => $this->course->id,
                'status' => $response['status'] ?? null,
                'uuid' => $response['body'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('NELC xAPI statement failed', [
                'type' => $this->type,
                'student_id' => $this->student->id,
                'course_id' => $this->course->id,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}