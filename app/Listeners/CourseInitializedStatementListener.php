<?php

namespace App\Listeners;

use App\Events\CourseInitialized;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class CourseInitializedStatementListener
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
     * @param  \App\Events\CourseInitialized  $event
     * @return void
     */
    public function handle(CourseInitialized $event)
    {
        SendNelcStatementJob::dispatch(
            $event->student,
            $event->course,
            'initialized'
        );
    }
}
