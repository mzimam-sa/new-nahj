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
    public function sendStatement($type, $student, $course, $object = null)
    {
        switch ($type) {

            case 'registered':
                return $this->xapi->Registered(
                    $student->userMetas->where('name', 'certificate_additional')->first(),
                    $student->email,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->teacher->full_name,
                    $course->teacher->email
                );

            case 'initialized':
                return $this->xapi->Initialized(
                    $student->userMetas->where('name', 'certificate_additional')->first(),
                    $student->email,
                    $course->id,
                    $course->title,
                    $course->description,
                    $course->teacher->full_name,
                    $course->teacher->email
                );

            // case 'watched':
            //     if (!$object) {
            //         throw new \Exception("Lesson is required for watched statements");
            //     }

            //     return $this->xapi->Watched(
            //         $student->userMetas->where('name', 'certificate_additional')->first(),
            //         $student->email,
            //         $object->id ?? $object->url,
            //         $object->title,
            //         $object->description,
            //         true,               // fully watched
            //         "PT15M",            // duration ISO 8601
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email
            //     );


            // case 'completed':
            //     if (!$object) {
            //         throw new \Exception("Lesson is required for completed statements");
            //     }

            //     return $this->xapi->CompletedLesson(
            //         $student->national_id,
            //         $student->email,
            //         $lesson->title,
            //         $lesson->description ?? '',
            //         $lesson->id ?? $lesson->url,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email
            //     );

                
            // case 'completed_course':
            //     if (!$lesson) {
            //         throw new \Exception("Lesson is required for completed statements");
            //     }

            //     return $this->xapi->CompletedCourse(
            //         $student->national_id,
            //         $student->email,
            //         $lesson->title,
            //         $lesson->description ?? '',
            //         $lesson->id ?? $lesson->url,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email
            //     );

            // case 'completed_unit':
            //     if (!$lesson) {
            //         throw new \Exception("Lesson is required for completed statements");
            //     }

            //     return $this->xapi->CompletedUnit(
            //         $student->national_id,
            //         $student->email,
            //         $lesson->title,
            //         $lesson->description ?? '',
            //         $lesson->id ?? $lesson->url,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email
            //     );

            case 'attempted':
                if (!$object) {
                    throw new \Exception("Quiz Result is required for attempted statements");
                }

                $raw = $object->user_grade;
                $min = 0;
                $max = $object->quiz->total_mark;

                $scaled = $max > 0 ? ($raw - $min) / ($max - $min) : 0;

                $success = $raw >= $object->quiz->pass_mark;
                $completion = true;

                $attemptNumber = data_get(
                    json_decode($object->results, true),
                    'attempt_number'
                );
                return $this->xapi->Attempted(
                    $student->userMetas->where('name', 'certificate_additional')->first(),
                    $student->email,
                    $object->quiz->id ?? $object->quiz->url,
                    $object->quiz->title,
                    $object->quiz->description ?? '',
                    $attemptNumber, 
                    $object->quiz->webinar->id, //course_id
                    $object->quiz->webinar->title, //course_title 
                    $object->quiz->webinar->description, //course_desc
                    $object->quiz->webinar->teacher->full_name,
                    $object->quiz->webinar->teacher->email,
                    $scaled,
                    $raw, 
                    $min, 
                    $max, 
                    $completion,
                    $success,

                );

            // case 'earned':

            //     return $this->xapi->Earned(
            //         $student->national_id,
            //         $student->email,
            //         $lesson->title,
            //         $lesson->description ?? '',
            //         $lesson->id ?? $lesson->url,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,

            //     );

            // case 'progressed':

            //     return $this->xapi->Progressed(
            //         $student->national_id,
            //         $student->email,
            //         $lesson->title,
            //         $lesson->description ?? '',
            //         $lesson->id ?? $lesson->url,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,

            //     );

            // case 'rated':

            //     return $this->xapi->Rated(
            //         $student->national_id,
            //         $student->email,
            //         $lesson->title,
            //         $lesson->description ?? '',
            //         $lesson->id ?? $lesson->url,
            //         $course->id,
            //         $course->title,
            //         $course->description,
            //         $course->instructor_name,
            //         $course->instructor_email,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,
            //         $course->instructor_name,

            //     );

            default:
                throw new \Exception("Unsupported statement type: {$type}");
        }
    }
}