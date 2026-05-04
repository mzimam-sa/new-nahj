<?php

namespace App\NelcXapi;

use App\NelcXapi\Interactions\Attempted;
use App\NelcXapi\Interactions\Completed;
use App\NelcXapi\Interactions\CompletedCourse;
use App\NelcXapi\Interactions\CompletedUnit;
use App\NelcXapi\Interactions\Earned;
use App\NelcXapi\Interactions\Initialized;
use App\NelcXapi\Interactions\Progressed;
use App\NelcXapi\Interactions\Rated;
use App\NelcXapi\Interactions\Registered;
use App\NelcXapi\Interactions\Watched;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class XapiIntegration
{
    protected $client;
    protected $headers;
    protected $url;
    protected $key;
    protected $secret;

    public function __construct()
    {
        $this->url = config('lrs-nelc-xapi.endpoint');
        $this->key = config('lrs-nelc-xapi.key');
        $this->secret = config('lrs-nelc-xapi.secret');

        $this->client = new Client([
            'auth' => [$this->key, $this->secret],
        ]);

        $this->headers = [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ];
    }

    public function Registered($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $programUrl = null)
    {
        $data = (new Registered())->Send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $programUrl);

        return $this->sendXAPIRequest($data);
    }

    public function Initialized($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        $data = (new Initialized())->Send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail);

        return $this->sendXAPIRequest($data);
    }

    public function Watched($actor, $actorEmail, $lessonUrl, $lessonTitle, $lessonDesc, bool $completion, $duration, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        $data = (new Watched())->Send($actor, $actorEmail, $lessonUrl, $lessonTitle, $lessonDesc, $completion, $duration, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail);

        return $this->sendXAPIRequest($data);
    }

    public function CompletedLesson($actor, $actorEmail, $lessonUrl, $lessonTitle, $lessonDesc, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        $data = (new Completed())->Send($actor, $actorEmail, $lessonUrl, $lessonTitle, $lessonDesc, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail);

        return $this->sendXAPIRequest($data);
    }

    public function CompletedUnit($actor, $actorEmail, $unitUrl, $unitTitle, $unitDesc, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        $data = (new CompletedUnit())->Send($actor, $actorEmail, $unitUrl, $unitTitle, $unitDesc, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail);

        return $this->sendXAPIRequest($data);
    }

    public function CompletedCourse($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail)
    {
        $data = (new CompletedCourse())->Send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail);

        return $this->sendXAPIRequest($data);
    }

    public function Progressed($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, bool $completion)
    {
        $data = (new Progressed())->Send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, $completion);

        return $this->sendXAPIRequest($data);
    }

    public function Attempted($actor, $actorEmail, $quizUrl, $quizTitle, $quizDesc, $attempNumber, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, $raw, $min, $max, bool $completion, bool $success)
    {
        $data = (new Attempted())->Send($actor, $actorEmail, $quizUrl, $quizTitle, $quizDesc, $attempNumber, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, $raw, $min, $max, $completion, $success);

        return $this->sendXAPIRequest($data);
    }

    public function Earned($actor, $actorEmail, $certUrl, $certStudentUrl, $certName, $courseId, $courseTitle, $courseDesc)
    {
        $data = (new Earned())->Send($actor, $actorEmail, $certUrl, $certStudentUrl, $certName, $courseId, $courseTitle, $courseDesc);

        return $this->sendXAPIRequest($data);
    }

    public function Rated($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, $raw, $comment)
    {
        $data = (new Rated())->Send($actor, $actorEmail, $courseId, $courseTitle, $courseDesc, $instructor, $instructorEmail, $scaled, $raw, $comment);

        return $this->sendXAPIRequest($data);
    }

    public function sendXAPIRequest($data = [])
    {
        $options = [
            'json' => $data,
            'headers' => $this->headers,
        ];

        try {
            $response = $this->client->post($this->url, $options);

            return [
                'status' => $response->getStatusCode(),
                'message' => $response->getReasonPhrase(),
                'body' => $response->getBody()->getContents(),
            ];
        } catch (RequestException $e) {
            $response = $e->getResponse();

            return [
                'status' => $response ? $response->getStatusCode() : 0,
                'message' => $response ? $response->getReasonPhrase() : 'Request Error',
                'body' => $response ? $response->getBody()->getContents() : $e->getMessage(),
            ];
        }
    }
}
