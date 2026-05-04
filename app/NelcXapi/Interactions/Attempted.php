<?php

namespace App\NelcXapi\Interactions;

class Attempted extends BrowserAwareInteraction
{
    public function Send($actor, $actorEmail, $quizUrl, $quizTitle, $quizDesc, $attempNumber, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, $raw, $min, $max, bool $completion, bool $success)
    {
        return [
            'actor' => [
                'name' => strval($actor),
                'mbox' => 'mailto:' . strval($actorEmail),
                'objectType' => 'Agent',
            ],
            'verb' => [
                'id' => 'http://adlnet.gov/expapi/verbs/attempted',
                'display' => ['en-US' => 'attempted'],
            ],
            'object' => [
                'id' => strval($quizUrl),
                'definition' => [
                    'name' => [strval($this->lang) => strval($quizTitle)],
                    'description' => [strval($this->lang) => strval($quizDesc)],
                    'type' => 'http://id.tincanapi.com/activitytype/unit-test',
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
                    'http://id.tincanapi.com/extension/attempt-id' => strval($attempNumber),
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
            'result' => [
                'score' => [
                    'scaled' => $scaled,
                    'raw' => $raw,
                    'min' => $min,
                    'max' => $max,
                ],
                'completion' => $completion,
                'success' => $success,
            ],
            'timestamp' => date('Y-m-d\\TH:i:s' . substr((string) microtime(), 1, 4) . '\\Z'),
        ];
    }
}
