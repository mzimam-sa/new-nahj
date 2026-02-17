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

    /**
     * Send xAPI statement to NELC
     *
     * @param string $type - registered, initialized, watched, completed
     * @param User $student
     * @param Course $course
     * @param Lesson|null $lesson - optional, used for watched/completed
     */
    public function sendStatement($type, $student, $course, $lesson = null)
    {
        switch ($type) {

            case 'registered':
                return $this->xapi->Registered(
                    $student->mobile,
                    $student->email,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->teacher->full_name,
                    $course->teacher->email
                );

            case 'initialized':
                return $this->xapi->Initialized(
                    $student->national_id,
                    $student->email,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email
                );

            case 'watched':
                if (!$lesson) {
                    throw new \Exception("Lesson is required for watched statements");
                }

                return $this->xapi->Watched(
                    $student->national_id,
                    $student->email,
                    $lesson->id ?? $lesson->url,
                    $lesson->title,
                    $lesson->description,
                    true,               // fully watched
                    "PT15M",            // duration ISO 8601
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email
                );

            case 'completed':
                if (!$lesson) {
                    throw new \Exception("Lesson is required for completed statements");
                }

                return $this->xapi->CompletedLesson(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email
                );

                
            case 'completed_course':
                if (!$lesson) {
                    throw new \Exception("Lesson is required for completed statements");
                }

                return $this->xapi->CompletedCourse(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email
                );

            case 'completed_unit':
                if (!$lesson) {
                    throw new \Exception("Lesson is required for completed statements");
                }

                return $this->xapi->CompletedUnit(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email
                );

            case 'attempted':

                return $this->xapi->Attempted(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,

                );

            case 'earned':

                return $this->xapi->Earned(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,

                );

            case 'progressed':

                return $this->xapi->Progressed(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,

                );

            case 'rated':

                return $this->xapi->Rated(
                    $student->national_id,
                    $student->email,
                    $lesson->title,
                    $lesson->description ?? '',
                    $lesson->id ?? $lesson->url,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->instructor_name,
                    $course->instructor_email,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,
                    $course->instructor_name,

                );

            default:
                throw new \Exception("Unsupported statement type: {$type}");
        }
    }
}