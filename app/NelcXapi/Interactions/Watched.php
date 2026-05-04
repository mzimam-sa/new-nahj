<?php

namespace App\NelcXapi\Interactions;

class Watched extends BrowserAwareInteraction
{
    public function Send($actor, $actorEmail, $lessonUrl, $lessonTitle, $lessonDesc, bool $completion, $duration, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        return [
            'actor' => [
                'name' => strval($actor),
                'mbox' => 'mailto:' . strval($actorEmail),
                'objectType' => 'Agent',
            ],
            'verb' => [
                'id' => 'https://w3id.org/xapi/acrossx/verbs/watched',
                'display' => ['en-US' => 'watched'],
            ],
            'object' => [
                'id' => strval($lessonUrl),
                'definition' => [
                    'name' => [strval($this->lang) => strval($lessonTitle)],
                    'description' => [strval($this->lang) => strval($lessonDesc)],
                    'type' => 'https://w3id.org/xapi/video/activity-type/video',
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
            'result' => [
                'completion' => $completion,
                'duration' => $duration,
            ],
            'timestamp' => date('Y-m-d\\TH:i:s' . substr((string) microtime(), 1, 4) . '\\Z'),
        ];
    }
}
