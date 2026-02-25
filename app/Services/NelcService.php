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

        $course->loadMissing('teacher', 'translations');

        $translation = $course->translate('ar') 
                    ?? $course->translate('en') 
                    ?? $course->translations->first();

        $courseTitle       = $translation?->title       ?? $course->slug ?? 'Untitled Course';
        $courseDescription = strip_tags($translation?->description ?? $courseTitle);

        $actorName = optional(
            $student->userMetas->where('name', 'certificate_additional')->first()
        )->value ?? $student->name ?? 'Unknown Student';

        $actorEmail      = str_replace('mailto:', '', $student->email);
        $instructorName  = $course->teacher?->full_name ?? 'Unknown Instructor';
        // $instructorEmail = str_replace('mailto:', '', $course->teacher?->email ?? 'noreply@nelc.gov.sa');
        $instructorEmail = $course->teacher?->email ?? 'noreply@nelc.gov.sa';
        switch ($type) {

            case 'registered':


                return $this->xapi->Registered(
                    $actorName,
                    $actorEmail,
                    $course->id,
                    $courseTitle,
                    $courseDescription,
                    $instructorName,
                    $instructorEmail,
                );
    
            case 'initialized':

                return $this->xapi->Initialized(
                      $actorName, $actorEmail, $course->id,
                        $courseTitle,
                        null, //$courseDescription
                        $instructorName, $instructorEmail,
                );

            case 'watched':
                if (!$object) {
                    throw new \Exception("Lesson is required for watched statements");
                }
                $object->loadMissing('chapter', 'translations');

                $translation = $object->translate('ar') 
                            ?? $object->translate('en') 
                            ?? $object->translations->first();

                $chapterTitle       = $translation?->title       ?? 'Untitled Course';
                $chapterDescription = strip_tags($translation?->title ?? $chapterTitle);

                return $this->xapi->Watched(
                    $actorName,$actorEmail,
                    $object->chapter->id , //lesson url
                    $chapterTitle, //lesson title
                    $chapterDescription, //lesson desc
                    true,               // fully watched
                    "PT15M",            // duration ISO 8601
                    $course->id,
                    $courseTitle, 
                    null, 
                    $instructorName, $instructorEmail,
                );


            case 'completed_lesson':
                if (!$object) {
                    throw new \Exception("Lesson is required for completed statements");
                }
                $object->loadMissing('chapter', 'translations');

                $translation = $object->translate('ar') 
                            ?? $object->translate('en') 
                            ?? $object->translations->first();

                $chapterTitle       = $translation?->title       ?? 'Untitled Course';
                $chapterDescription = strip_tags($translation?->title ?? $chapterTitle);

                return $this->xapi->CompletedLesson(
                   $actorName, $actorEmail,
                    $object->chapter->id,
                    $chapterTitle,
                    $chapterDescription,        
                    $course->id,
                    $courseTitle, null,
                    $instructorName, $instructorEmail,
                            );

            case 'completed_course':

                return $this->xapi->CompletedCourse(
                    $actorName, $actorEmail,
                    $course->id,
                    $courseTitle, null,
                    $instructorName, $instructorEmail,
                );

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
                    throw new \Exception("Quiz Result is required");
                }

                $object->loadMissing('quiz.translations');

                $quiz = $object->quiz;

                $quizTranslation = $quiz->translate('ar') 
                                ?? $quiz->translate('en') 
                                ?? $quiz->translations->first();

                $quizTitle       = $quizTranslation?->title ?? 'Untitled Quiz';
                $quizDescription = strip_tags($quizTranslation?->description ?? $quizTitle);

                $raw     = $object->user_grade;
                $max     = $quiz->total_mark;
                $min     = 0;
                $scaled  = $max > 0 ? round(($raw - $min) / ($max - $min), 2) : 0;
                $success = $raw >= $quiz->pass_mark;

                $attemptNumber = data_get(json_decode($object->results, true), 'attempt_number') ?? 1;

                // ✅ استخدم الـ $course اللي ممرره بدل quiz->webinar
                return $this->xapi->Attempted(
                    $actorName, $actorEmail,
                    $quiz->id,
                    $quizTitle,
                    $quizDescription,
                    $attemptNumber,
                    $course->id,        // ✅ من الـ $course المُمرر
                    $courseTitle,
                    null,
                    $instructorName,    // ✅ من الـ $course المُمرر
                    $instructorEmail,
                    $scaled,
                    $raw,
                    $min,
                    $max,
                    true,
                    $success,
                );

            case 'earned':

                $certName = $courseTitle; // اسم الشهادة = اسم الكورس

                // ✅ رابط الشهادة العام بدون لوجين
                $certUrl  = url('/certificate/' . $course->id . '/' . $student->id);

                return $this->xapi->Earned(
                    $actorName,
                    $actorEmail,
                    $certUrl,
                    $certName,
                    $course->id,
                    $courseTitle,
                    null,
                );

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