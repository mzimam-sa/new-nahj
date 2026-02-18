<?php

namespace App\Listeners;

use App\Events\CompletedLesson;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class CompletedLessonStatementListener
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
        SendNelcStatementJob::dispatch(
            $event->student,
            $event->course,
            'completed'
        );
    }
}
