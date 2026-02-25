<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\Api\User;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use Illuminate\Contracts\Queue\ShouldQueue;
class CompletedLesson implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $student;
    public $course;
    public $assignment;

    public function __construct(User $student, Webinar $course,WebinarAssignment $assignment)
    {
        $this->student = $student;
        $this->course = $course;
        $this->assignment = $assignment;
    }
}