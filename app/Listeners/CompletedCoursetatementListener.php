<?php

namespace App\Listeners;

use App\Events\CompletedCourse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class CompletedCoursetatementListener implements ShouldQueue
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
     * @param  \App\Events\CompletedCourse  $event
     * @return void
     */
    public function handle(CompletedCourse $event)
    {
        SendNelcStatementJob::dispatch(
            'completed_course',
            $event->student,
            $event->course,
        );
    }
}
