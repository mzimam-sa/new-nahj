<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \App\Events\StudentEnrolled::class => [
        \App\Listeners\StudentEnrolledStatementListener::class,
        ],

        \App\Events\CourseInitialized::class => [
        \App\Listeners\CourseInitializedStatementListener::class,
        ],

        \App\Events\ModuleWatched::class => [
        \App\Listeners\ModuleWatchedStatementListener::class,
        ],

        \App\Events\CompletedLesson::class => [
        \App\Listeners\CompletedLessonStatementListener::class,
        ],

        \App\Events\CompletedCourse::class => [
        \App\Listeners\CompletedCoursetatementListener::class,
        ],

        \App\Events\QuizAttempted::class => [
        \App\Listeners\QuizAttemptedStatementListener::class,
        ],
        
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
