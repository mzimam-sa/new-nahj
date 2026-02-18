<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\User;
use App\Models\Webinar;
use App\Models\QuizzesResult;
use Illuminate\Contracts\Queue\ShouldQueue;

class QuizAttempted implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $student;
    public $course;
    public $quizResult;

    public function __construct(User $student, Webinar $course,QuizzesResult $quizResult)
    {
        $this->student = $student;
        $this->course = $course;
        $this->quizResult = $quizResult;
    }

}