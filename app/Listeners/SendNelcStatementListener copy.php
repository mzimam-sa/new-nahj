<?php

namespace App\Listeners;

use App\Events\StudentEnrolled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class SendNelcStatementListener
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
        SendNelcStatementJob::dispatch(
            $event->student,
            $event->course,
            'registered'
        );
    }
}
