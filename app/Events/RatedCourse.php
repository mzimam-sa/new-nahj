<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\User;
use App\Models\Webinar;
use Illuminate\Contracts\Queue\ShouldQueue;

class RatedCourse implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $student;
    public $course;

    public function __construct(User $student, ?Webinar $course)
    {
        $this->student = $student;
        $this->course = $course;
    }
}