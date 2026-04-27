<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$student = App\User::find(1077);
$course = App\Models\Webinar::with('teacher', 'translations')->find(2036);
$assignment = App\Models\WebinarAssignment::with('chapter', 'translations')->find(6);
$quizResult = App\Models\QuizzesResult::with('quiz')->find(56);
$nelc = app(App\Services\NelcService::class);

$results['registered'] = $nelc->sendStatement('registered', $student, $course);
$results['initialized'] = $nelc->sendStatement('initialized', $student, $course);
$results['watched'] = $nelc->sendStatement('watched', $student, $course, $assignment);
$results['progressed_33'] = $nelc->sendStatement('progressed', $student, $course,null, 0.33);
$results['completed_lesson'] = $nelc->sendStatement('completed_lesson', $student, $course, $assignment);
$results['attempted'] = $nelc->sendStatement('attempted', $student, $course, $quizResult);
$results['progressed_66'] = $nelc->sendStatement('progressed', $student, $course,null, 0.66);
$results['completed_course'] = $nelc->sendStatement('completed_course', $student, $course);
$results['progressed_100'] = $nelc->sendStatement('progressed', $student, $course,null, 1.0);
$results['rated'] = $nelc->sendStatement('rated', $student, $course);
$results['earned'] = $nelc->sendStatement('earned', $student, $course);

echo '<pre>';
print_r($results);
echo '</pre>';
