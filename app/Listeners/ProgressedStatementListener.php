<?php

namespace App\Listeners;

use App\Events\Progressed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\ModuleWatchedStatementJob;

class ProgressedStatementListener implements ShouldQueue
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
     * @param  \App\Events\Progressed  $event
     * @return void
     */
    public function handle(Progressed $event)
    {
        \Log::info("Listener Fired - Progressed");
        ModuleWatchedStatementJob::dispatch(
            'progressed',
            $event->student,
            $event->course,
            $event->scaled,
        )->delay(now()->addMinute(3));
    }
}
