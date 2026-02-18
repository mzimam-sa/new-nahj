<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\User;
use App\Models\Webinar;

class StudentEnrolled 
{
    use Dispatchable, SerializesModels;

    public $student;
    public $course;

    public function __construct(User $student, Webinar $course)
    {
        $this->student = $student;
        $this->course = $course;
    }
}