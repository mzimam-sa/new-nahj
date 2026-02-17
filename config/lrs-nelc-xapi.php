<?php
return [
    'endpoint'      => env('LRS_ENDPOINT'),
    'middleware'      => ['web'],
    'key'    => env('LRS_USERNAME'),
    'secret'    => env('LRS_PASSWORD'),
    'platform_in_arabic'    => 'نهج المعرفة للتدريب', // Platform name in Arabic
    'platform_in_english'    => 'https://nahj.com.sa/', // Platform name in English
    'base_route'    => 'nelcxapi/test', // Demo Page Link
];
