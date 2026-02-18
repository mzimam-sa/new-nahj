<?php

namespace App\Listeners;

use App\Events\StudentEnrolled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class StudentEnrolledStatementListener implements ShouldQueue
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
     * @param  \App\Events\StudentEnrolled  $event
     * @return void
     */
    public function handle(StudentEnrolled $event)
    {
        \Log::info("Listener Fired");
        SendNelcStatementJob::dispatch(
            $event->student,
            $event->course,
            'registered'
        );
    }
}
