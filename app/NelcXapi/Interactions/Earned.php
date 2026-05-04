<?php

namespace App\NelcXapi\Interactions;

class Earned extends BaseInteraction
{
    public function Send($actor, $actorEmail, $certUrl, $certStudentUrl, $certName, $courseId, $courseTitle, $courseDesc)
    {
        return [
            'actor' => [
                'name' => strval($actor),
                'mbox' => 'mailto:' . strval($actorEmail),
                'objectType' => 'Agent',
            ],
            'verb' => [
                'id' => 'http://id.tincanapi.com/verb/earned',
                'display' => ['en-US' => 'earned'],
            ],
            'object' => [
                'id' => strval($certUrl),
                'definition' => [
                    'name' => [strval($this->lang) => strval($certName)],
                    'type' => 'https://www.opigno.org/en/tincan_registry/activity_type/certificate',
                ],
                'objectType' => 'Activity',
            ],
            'context' => [
                'extensions' => [
                    'http://id.tincanapi.com/extension/jws-certificate-location' => strval($certStudentUrl),
                    'https://nelc.gov.sa/extensions/platform' => [
                        'name' => [
                            'ar-SA' => strval($this->platform_in_arabic),
                            'en-US' => strval($this->platform_in_english),
                        ],
                    ],
                ],
                'platform' => strval($this->platform),
                'language' => strval($this->lang),
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
