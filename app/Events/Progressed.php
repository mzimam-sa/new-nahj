<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\Api\User;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use Illuminate\Contracts\Queue\ShouldQueue;

class Progressed implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $student;
    public $course;
    public $scaled;

    public function __construct(User $student, Webinar $course,$scaled)
    {
        $this->student = $student;
        $this->course = $course;
        $this->scaled = $scaled;
    }
}