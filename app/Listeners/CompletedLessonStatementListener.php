<?php

namespace App\Listeners;

use App\Events\CompletedLesson;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\ModuleWatchedStatementJob;
class CompletedLessonStatementListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CompletedLesson  $event
     * @return void
     */
    public function handle(CompletedLesson $event)
    {
        \Log::info("Listener Fired - Completed Lesson");
        ModuleWatchedStatementJob::dispatch(
            'completed_lesson',
            $event->student,
            $event->course,
            $event->assignment,
        );
    }
}
