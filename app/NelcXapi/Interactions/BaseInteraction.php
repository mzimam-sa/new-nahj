<?php

namespace App\NelcXapi\Interactions;

use Illuminate\Support\Facades\App;

abstract class BaseInteraction
{
    protected $platform_in_arabic;
    protected $platform_in_english;
    protected $platform;
    protected $lms_url;
    protected $lang;

    public function __construct()
    {
        $this->lms_url = config('lrs-nelc-xapi.lms_url') ?: config('app.url');
        $this->platform_in_arabic = $this->resolvePlatformValue('platform_in_arabic');
        $this->platform_in_english = $this->resolvePlatformValue('platform_in_english');
        $this->platform = App::getLocale() === 'ar' ? $this->platform_in_arabic : $this->platform_in_english;
        $this->lang = App::getLocale() === 'ar' ? 'ar-SA' : 'en-US';
    }

    protected function resolvePlatformValue(string $key): string
    {
        return (string) (
            config('lrs-nelc-xapi.platform')
            ?: config("lrs-nelc-xapi.{$key}")
            ?: config('lrs-nelc-xapi.lms_url')
            ?: config('app.url')
            ?: env('LRS_PLATFORM')
            ?: env('APP_URL')
            ?: 'https://www.nahj.com.sa'
        );
    }
}
