<?php

namespace App\Listeners;

use App\Events\RatedCourse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class RatedCourseStatementListener implements ShouldQueue
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
     * @param  \App\Events\RatedCourse  $event
     * @return void
     */
    public function handle(RatedCourse $event)
    {
        \Log::info("Listener Fired - Rated Course");
        SendNelcStatementJob::dispatch(
            'rated',
            $event->student,
            $event->course,
        )->delay(now()->addMinute(5));
    }
}
