<?php

namespace App\Listeners;

use App\Events\EarnedCertificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendNelcStatementJob;

class EarnedCertificateStatementListener implements ShouldQueue
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
     * @param  \App\Events\EarnedCertificate  $event
     * @return void
     */
    public function handle(EarnedCertificate $event)
    {
        \Log::info("Listener Fired - Earned Certificate");
        SendNelcStatementJob::dispatch(
            'earned',
            $event->student,
            $event->course,
        )->delay(now()->addMinute(5));
    }
}
