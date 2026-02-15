<?php

namespace App\Services;

use Nelc\LaravelNelcXapiIntegration\XapiIntegration;

class NelcService
{
    protected $xapi;

    public function __construct()
    {
        $this->xapi = new XapiIntegration();
    }

    public function sendStatement($type, $student, $course)
    {
        switch ($type) {

            case 'registered':
                //Learner registers/enrolls in the content
                return $this->xapi->Registered(
                    $student->national_id, // Student National ID
                    $student->email, // Student Email
                    $course->id, // Course Id OR url Or slug
                    $course->title, // Course Title
                    $course->description, // Course description
                    $course->instructor_name, // instructor Name
                    $course->instructor_email // instructor Email
                );

            // case 'completed':
            //     return $this->xapi->Completed(
            //         $student->national_id,
            //         $student->email,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email
            //     );

            default:
                throw new \Exception("Unsupported statement type: {$type}");
        }
    }
   
}