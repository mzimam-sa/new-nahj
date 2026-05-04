<?php

namespace App\NelcXapi\Interactions;

use Jenssegers\Agent\Agent;

abstract class BrowserAwareInteraction extends BaseInteraction
{
    protected $browserName;
    protected $browserVersion;
    protected $browserCode;

    public function __construct()
    {
        parent::__construct();

        $agent = new Agent();
        $this->browserName = $agent->browser();
        $this->browserVersion = $agent->version($this->browserName);
        $this->browserCode = $agent->platform();
    }
}
