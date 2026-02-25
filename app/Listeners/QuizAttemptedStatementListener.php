<?php

namespace App\Listeners;

use App\Events\QuizAttempted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\QuizAttemptedStatementJob;

class QuizAttemptedStatementListener
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
     * @param  \App\Events\QuizAttempted  $event
     * @return void
     */
    public function handle(QuizAttempted $event)
    {
        QuizAttemptedStatementJob::dispatch(
            'quiz',
            $event->student,
            $event->course,
            $event->quizResult,
        );
    }
}
