<?php

namespace App\Listeners;

use App\Events\ModuleWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\ModuleWatchedStatementJob;

class ModuleWatchedStatementListener implements ShouldQueue
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
     * @param  \App\Events\ModuleWatched  $event
     * @return void
     */
    public function handle(ModuleWatched $event)
    {
        ModuleWatchedStatementJob::dispatch(
            'watched',
            $event->student,
            $event->course,
            $event->assignment,
        );
    }
}
