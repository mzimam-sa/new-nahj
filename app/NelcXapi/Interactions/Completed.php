<?php

namespace App\NelcXapi\Interactions;

class Completed extends BrowserAwareInteraction
{
    public function Send($actor, $actorEmail, $lessonUrl, $lessonTitle, $lessonDesc, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        return [
            'actor' => [
                'name' => strval($actor),
                'mbox' => 'mailto:' . strval($actorEmail),
                'objectType' => 'Agent',
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => ['en-US' => 'completed'],
            ],
            'object' => [
                'id' => strval($lessonUrl),
                'definition' => [
                    'name' => [strval($this->lang) => strval($lessonTitle)],
                    'description' => [strval($this->lang) => strval($lessonDesc)],
                    'type' => 'http://adlnet.gov/expapi/activities/lesson',
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
                    'http://id.tincanapi.com/extension/browser-info' => [
                        'code_name' => strval($this->browserCode),
                        'name' => strval($this->browserName),
                        'version' => strval($this->browserVersion),
                    ],
                    'https://nelc.gov.sa/extensions/platform' => [
                        'name' => [
                            'ar-SA' => strval($this->platform_in_arabic),
                            'en-US' => strval($this->platform_in_english),
                        ],
                    ],
                ],
                'contextActivities' => [
                    'parent' => [
                        [
                            'id' => strval($courseId),
                            'definition' => [
                                'name' => [strval($this->lang) => strval($courseTitle)],
                                'description' => [strval($this->lang) => strval($courseDesc)],
                                'type' => 'https://w3id.org/xapi/cmi5/activitytype/course',
                            ],
                            'objectType' => 'Activity',
                        ],
                    ],
                ],
            ],
            'timestamp' => date('Y-m-d\\TH:i:s' . substr((string) microtime(), 1, 4) . '\\Z'),
        ];
    }
}
