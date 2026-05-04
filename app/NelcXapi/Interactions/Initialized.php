<?php

namespace App\NelcXapi\Interactions;

class Initialized extends BaseInteraction
{
    public function Send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        return [
            'actor' => [
                'name' => strval($actor),
                'mbox' => 'mailto:' . strval($actorEmail),
                'objectType' => 'Agent',
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/initialized',
                'display' => ['en-US' => 'initialized'],
            ],
            'object' => [
                'id' => strval($courseId),
                'definition' => [
                    'name' => [strval($this->lang) => strval($courseTitle)],
                    'description' => [strval($this->lang) => strval($courseDesc)],
                    'type' => 'https://w3id.org/xapi/cmi5/activitytype/course',
                ],
                'objectType' => 'Activity',
            ],
            'context' => [
                'instructor' => [
                    'name' => strval($instructor),
                    'mbox' => 'mailto:' . strval($instructorEmail),
                ],
                'platform' => strval($this->platform),
                'language' => strval($this->lang),
                'extensions' => [
                    'https://nelc.gov.sa/extensions/platform' => [
                        'name' => [
                            'ar-SA' => strval($this->platform_in_arabic),
                            'en-US' => strval($this->platform_in_english),
                        ],
                    ],
                ],
            ],
            'timestamp' => date('Y-m-d\\TH:i:s' . substr((string) microtime(), 1, 4) . '\\Z'),
        ];
    }
}
