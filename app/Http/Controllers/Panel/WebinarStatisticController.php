<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Imports\WebinarSaleImport;
use App\Models\Comment;
use App\Models\CourseForum;

use App\Models\Gift;
use App\Models\InstallmentOrder;
use App\Models\Quiz;
use App\Models\QuizzesResult;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use App\Models\WebinarAssignmentHistory;
use App\User;

use App\Models\Accounting;
use App\Models\Bundle;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ReserveMeeting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class WebinarStatisticController extends Controller
{

    public function index(Request $request, $webinarId)
    {
        $user = auth()->user();

        $webinar = Webinar::where('id', $webinarId)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                });

                $query->orWhereHas('webinarPartnerTeacher', function ($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                });
            })
            ->with([
                'chapters' => function ($query) {
                    $query->where('status', 'active');
                },
                'sessions' => function ($query) {
                    $query->where('status', 'active');
                },
                'assignments' => function ($query) {
                    $query->where('status', 'active');
                },
                'quizzes' => function ($query) {
                    $query->where('status', 'active');
                },
                'files' => function ($query) {
                    $query->where('status', 'active');
                },
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->first();

        if (!empty($webinar)) {
            $studentsIds = Sale::where('webinar_id', $webinarId)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $gifts = Gift::query()->where('webinar_id', $webinar->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->get();

            $installmentStudentIds = InstallmentOrder::query()
                ->where('webinar_id', $webinar->id)
                ->where('status', 'open')
                ->pluck('user_id')
                ->toArray();

            $studentsIds = array_merge($studentsIds, $installmentStudentIds);

            $getStudents = $this->getStudents($request, $webinar, $studentsIds, $gifts);

            $data = [
                'pageTitle' => trans('update.course_statistics'),
                'webinar' => $webinar,
                'students' => $getStudents['users'],
                'unregisteredUsers' => $getStudents['unregisteredUsers'],
                'studentsCount' => count(array_unique($studentsIds)) + count($gifts),
                'commentsCount' => $this->getCommentsCount($webinarId),
                'salesCount' => count($studentsIds) + count($gifts),
                'salesAmount' => $this->getSalesAmounts($webinarId, $gifts->pluck('id')->toArray()),
                'chaptersCount' => $webinar->chapters->count(),
                'sessionsCount' => $webinar->sessions->count(),
                'pendingQuizzesCount' => $this->getPendingQuizzesCount($webinarId),
                'pendingAssignmentsCount' => $this->getPendingAssignmentsCount($webinarId),
                'courseRate' => $webinar->getRate(),
                'courseRateCount' => $webinar->reviews->count(),
                'quizzesAverageGrade' => $this->getQuizzesAverageGrade($webinarId),
                'assignmentsAverageGrade' => $this->getAssignmentsAverageGrade($webinarId),
                'courseForumsMessagesCount' => $this->getCourseForumsMessagesCount($webinarId),
                'courseForumsStudentsCount' => $this->getCourseForumsStudentsCount($webinarId),
                'studentsUserRolesChart' => $this->handleStudentsUserRolesChart($studentsIds),
                'courseProgressChart' => $this->handleCourseProgressChart($webinar, $studentsIds),
                'quizStatusChart' => $this->handleQuizStatusChart($webinar),
                'assignmentsStatusChart' => $this->handleAssignmentsStatusChart($webinar),
                'monthlySalesChart' => $this->getMonthlySalesChart($webinarId),
                'courseProgressLineChart' => $this->handleCourseProgressLineChart($webinar, $studentsIds),
            ];

            return view('web.default.panel.webinar.course_statistics.index', $data);
        }

        abort(404);
    }

    private function getStudents(Request $request, $webinar, $studentsIds, $gifts)
    {
        $receiptsGift = [];
        $unregisteredGift = [];

        foreach ($gifts as $gift) {
            $receipt = $gift->receipt;

            if (!empty($receipt)) {
                $receiptsGift[] = $receipt->id;
            } else {
                $unregisteredGift[] = $gift;
            }
        }

        $studentsIds = array_merge($studentsIds, $receiptsGift);

        $users = User::query()->whereIn('id', $studentsIds)
            ->paginate(10);

        $quizzesIds = $webinar->quizzes->pluck('id')->toArray();
        $assignmentsIds = $webinar->assignments->pluck('id')->toArray();

        foreach ($users as $user) {
            $user->course_progress = $this->getCourseProgressForStudent($webinar, $user->id);

            $user->passed_quizzes = Quiz::whereIn('quizzes.id', $quizzesIds)
                ->join('quizzes_results', 'quizzes_results.quiz_id', 'quizzes.id')
                ->select(DB::raw('count(quizzes_results.id) as count'))
                ->where('quizzes_results.user_id', $user->id)
                ->where('quizzes_results.status', QuizzesResult::$passed)
                ->first()->count;

            $assignmentsHistoriesCount = WebinarAssignmentHistory::whereIn('assignment_id', $assignmentsIds)
                ->where('student_id', $user->id)
                ->count();

            $user->unsent_assignments = count($assignmentsIds) - $assignmentsHistoriesCount;

            $user->pending_assignments = WebinarAssignmentHistory::whereIn('assignment_id', $assignmentsIds)
                ->where('student_id', $user->id)
                ->where('status', WebinarAssignmentHistory::$pending)
                ->count();
        }

        $unregisteredUsers = Collection::make(new User());

        if (count($unregisteredGift) and $request->get('page', 1) == 1) {
            foreach ($unregisteredGift as $item) {
                $newUser = new User();
                $newUser->full_name = $item->name;
                $newUser->email = $item->email;

                $unregisteredUsers = $unregisteredUsers->push($newUser);
            }
        }

        return [
            'users' => $users,
            'unregisteredUsers' => $unregisteredUsers,
        ];
    }

    private function getCommentsCount($webinarId)
    {
        return Comment::where('webinar_id', $webinarId)
            ->where('status', 'active')
            ->count();
    }

    private function getSalesAmounts($webinarId, $giftsIds)
    {
        return Sale::query()
            ->where(function ($query) use ($webinarId, $giftsIds) {
                $query->where('webinar_id', $webinarId);
                $query->orWhereIn('gift_id', $giftsIds);
            })
            ->whereNull('refund_at')
            ->sum('total_amount');
    }

    private function getPendingQuizzesCount($webinarId)
    {
        return Quiz::where('webinar_id', $webinarId)
            ->where('status', 'active')
            ->whereHas('quizResults', function ($query) {
                $query->where('status', 'waiting');
            })
            ->count();
    }

    private function getPendingAssignmentsCount($webinarId)
    {
        return WebinarAssignment::where('webinar_id', $webinarId)
            ->where('status', 'active')
            ->whereHas('assignmentHistory', function ($query) {
                $query->where('status', 'pending');
            })
            ->count();
    }

    private function getQuizzesAverageGrade($webinarId)
    {
        $quizzes = Quiz::where('webinar_id', $webinarId)
            ->join('quizzes_results', 'quizzes_results.quiz_id', 'quizzes.id')
            ->select(DB::raw('avg(quizzes_results.user_grade) as result_grade'))
            ->whereIn('quizzes_results.status', ['passed', 'failed'])
            ->groupBy('quizzes_results.quiz_id')
            ->get();

        return $quizzes->avg('result_grade');
    }

    private function getAssignmentsAverageGrade($webinarId)
    {
        $assignments = WebinarAssignment::where('webinar_id', $webinarId)
            ->join('webinar_assignment_history', 'webinar_assignment_history.assignment_id', 'webinar_assignments.id')
            ->select(DB::raw('avg(webinar_assignment_history.grade) as result_grade'))
            ->whereIn('webinar_assignment_history.status', ['passed', 'not_passed'])
            ->groupBy('webinar_assignment_history.assignment_id')
            ->get();

        return $assignments->avg('result_grade') ?? 0;
    }

    private function getCourseForumsMessagesCount($webinarId)
    {
        $forums = CourseForum::where('webinar_id', $webinarId)
            ->join('course_forum_answers', 'course_forum_answers.forum_id', 'course_forums.id')
            ->select(DB::raw('count(course_forum_answers.id) as count'))
            ->groupBy('course_forum_answers.forum_id')
            ->get();

        return $forums->sum('count') ?? 0;
    }

    private function getCourseForumsStudentsCount($webinarId)
    {
        $forums = CourseForum::where('webinar_id', $webinarId)
            ->join('course_forum_answers', 'course_forum_answers.forum_id', 'course_forums.id')
            ->select(DB::raw('count(distinct course_forum_answers.user_id) as count'))
            ->groupBy('course_forum_answers.forum_id')
            ->get();

        return $forums->sum('count') ?? 0;
    }

    private function handleStudentsUserRolesChart($studentsIds)
    {
        $labels = [
            trans('public.students'),
            trans('public.instructors'),
            trans('home.organizations'),
        ];

        $users = User::whereIn('id', $studentsIds)
            ->select('role_name', DB::raw('count(id) as count'))
            ->groupBy('role_name')
            ->get();

        $data = [0, 0, 0];

        foreach ($users as $user) {
            if ($user->role_name == Role::$user) {
                $data[0] = $user->count;
            } else if ($user->role_name == Role::$teacher) {
                $data[1] = $user->count;
            } else if ($user->role_name == Role::$organization) {
                $data[2] = $user->count;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function handleQuizStatusChart($webinar)
    {
        $labels = [
            trans('quiz.passed'),
            trans('public.pending'),
            trans('quiz.failed'),
        ];

        $data[0] = 0; // passed
        $data[1] = 0; // pending
        $data[2] = 0; // failed

        $quizzes = $webinar->quizzes;

        foreach ($quizzes as $quiz) {
            $passed = $quiz->quizResults()->where('status', QuizzesResult::$passed)->count();
            $pending = $quiz->quizResults()->where('status', QuizzesResult::$waiting)->count();
            $failed = $quiz->quizResults()->where('status', QuizzesResult::$failed)->count();

            $data[0] += $passed;
            $data[1] += $pending;
            $data[2] += $failed;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function handleAssignmentsStatusChart($webinar)
    {
        $labels = [
            trans('quiz.passed'),
            trans('public.pending'),
            trans('quiz.failed'),
        ];

        $data[0] = 0; // passed
        $data[1] = 0; // pending
        $data[2] = 0; // failed

        $assignments = $webinar->assignments;

        foreach ($assignments as $quiz) {
            $passed = $quiz->assignmentHistory()->where('status', WebinarAssignmentHistory::$passed)->count();
            $pending = $quiz->assignmentHistory()->where('status', WebinarAssignmentHistory::$pending)->count();
            $failed = $quiz->assignmentHistory()->where('status', WebinarAssignmentHistory::$notPassed)->count();

            $data[0] += $passed;
            $data[1] += $pending;
            $data[2] += $failed;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getMonthlySalesChart($webinarId)
    {
        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(date('Y'), $month);

            $start_date = $date->timestamp;
            $end_date = $date->copy()->endOfMonth()->timestamp;

            $labels[] = trans('panel.month_' . $month);

            $amount = Sale::whereNull('refund_at')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->where('webinar_id', $webinarId)
                ->sum('total_amount');

            $data[] = round($amount, 2);
        }


        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getCourseProgressForStudent($webinar, $userId)
    {
        $progress = 0;

        $filesStat = $webinar->getFilesLearningProgressStat($userId);
        $sessionsStat = $webinar->getSessionsLearningProgressStat($userId);
        $textLessonsStat = $webinar->getTextLessonsLearningProgressStat($userId);
        $assignmentsStat = $webinar->getAssignmentsLearningProgressStat($userId);
        $quizzesStat = $webinar->getQuizzesLearningProgressStat($userId);

        $passed = $filesStat['passed'] + $sessionsStat['passed'] + $textLessonsStat['passed'] + $assignmentsStat['passed'] + $quizzesStat['passed'];
        $count = $filesStat['count'] + $sessionsStat['count'] + $textLessonsStat['count'] + $assignmentsStat['count'] + $quizzesStat['count'];

        if ($passed > 0 and $count > 0) {
            $progress = ($passed * 100) / $count;
        }

        return round($progress, 2);
    }

    public function handleCourseProgressChart($webinar, $studentsIds)
    {
        $labels = [
            trans('update.completed'),
            trans('webinars.in_progress'),
            trans('update.not_started'),
        ];

        $data[0] = 0; // completed
        $data[1] = 0; // in_progress
        $data[2] = 0; // not_started

        foreach ($studentsIds as $userId) {

            $progress = $this->getCourseProgressForStudent($webinar, $userId);

            if ($progress > 0 and $progress < 100) {
                $data[1] += 1;
            } elseif ($progress == 100) {
                $data[0] += 1;
            } else {
                $data[2] += 1;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function handleCourseProgressLineChart($webinar, $studentsIds)
    {
        $labels = [];
        $data = [];

        $progress = [];

        foreach ($studentsIds as $userId) {
            $progress[] = $this->getCourseProgressForStudent($webinar, $userId);
        }

        for ($percent = 0; $percent < 100; $percent += 10) {
            $endPercent = $percent + 10;
            $labels[] = $percent . '-' . $endPercent;

            $count = 0;

            foreach ($progress as $value) {
                if ($value >= $percent and $value < $endPercent) {
                    $count += 1;
                }
            }

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function ShowStudents(Request $request, $webinarId)
    {
        $user = auth()->user();

        $webinar = Webinar::where('id', $webinarId)
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                });

                $query->orWhereHas('webinarPartnerTeacher', function ($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                });
            })
            ->with([
                'chapters' => function ($query) {
                    $query->where('status', 'active');
                },
                'sessions' => function ($query) {
                    $query->where('status', 'active');
                },
                'assignments' => function ($query) {
                    $query->where('status', 'active');
                },
                'quizzes' => function ($query) {
                    $query->where('status', 'active');
                },
                'files' => function ($query) {
                    $query->where('status', 'active');
                },
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->first();

        if (!empty($webinar)) {
            $studentsIds = Sale::where('webinar_id', $webinarId)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $gifts = Gift::query()->where('webinar_id', $webinar->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->get();

            $installmentStudentIds = InstallmentOrder::query()
                ->where('webinar_id', $webinar->id)
                ->where('status', 'open')
                ->pluck('user_id')
                ->toArray();

            $studentsIds = array_merge($studentsIds, $installmentStudentIds);

            $getStudents = $this->getStudents($request, $webinar, $studentsIds, $gifts);

            $data = [
                'pageTitle' => trans('update.course_statistics'),
                'webinar' => $webinar,
                'students' => $getStudents['users'],
                'unregisteredUsers' => $getStudents['unregisteredUsers'],
                'studentsCount' => count(array_unique($studentsIds)) + count($gifts),
                'commentsCount' => $this->getCommentsCount($webinarId),
                'salesCount' => count($studentsIds) + count($gifts),
                'salesAmount' => $this->getSalesAmounts($webinarId, $gifts->pluck('id')->toArray()),
                'chaptersCount' => $webinar->chapters->count(),
                'sessionsCount' => $webinar->sessions->count(),
                'pendingQuizzesCount' => $this->getPendingQuizzesCount($webinarId),
                'pendingAssignmentsCount' => $this->getPendingAssignmentsCount($webinarId),
                'courseRate' => $webinar->getRate(),
                'courseRateCount' => $webinar->reviews->count(),
                'quizzesAverageGrade' => $this->getQuizzesAverageGrade($webinarId),
                'assignmentsAverageGrade' => $this->getAssignmentsAverageGrade($webinarId),
                'courseForumsMessagesCount' => $this->getCourseForumsMessagesCount($webinarId),
                'courseForumsStudentsCount' => $this->getCourseForumsStudentsCount($webinarId),
                'studentsUserRolesChart' => $this->handleStudentsUserRolesChart($studentsIds),
                'courseProgressChart' => $this->handleCourseProgressChart($webinar, $studentsIds),
                'quizStatusChart' => $this->handleQuizStatusChart($webinar),
                'assignmentsStatusChart' => $this->handleAssignmentsStatusChart($webinar),
                'monthlySalesChart' => $this->getMonthlySalesChart($webinarId),
                'courseProgressLineChart' => $this->handleCourseProgressLineChart($webinar, $studentsIds),
            ];

            return view('web.default.panel.webinar.course_statistics.ShowStudents', $data);
        }

        abort(404);
    }

    public function addStudent(Request $request)
    {
//        $this->authorize('admin_enrollment_add_student_to_items');

        $data = $request->all();
//        dd($data);// user_id , webinar_id

        $rules = [
            'user_id' => 'required|exists:users,id',
        ];

        if (!empty($data['webinar_id'])) {
            $rules['webinar_id'] = 'required|exists:webinars,id';
        } elseif (!empty($data['bundle_id'])) {
            $rules['bundle_id'] = 'required|exists:bundles,id';
        } elseif (!empty($data['product_id'])) {
            $rules['product_id'] = 'required|exists:products,id';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response([
                    'code' => 422,
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                return back()->withErrors($validator->errors()->getMessages());
            }
        }

        $user = User::find($data['user_id']);

        if (!empty($user)) {
            $sellerId = null;
            $itemType = null;
            $itemId = null;
            $itemColumnName = null;
            $checkUserHasBought = false;
            $isOwner = false;
            $product = null;

            if (!empty($data['webinar_id'])) {
                $course = Webinar::find($data['webinar_id']);

                if (!empty($course)) {
                    $sellerId = $course->creator_id;
                    $itemId = $course->id;
                    $itemType = Sale::$webinar;
                    $itemColumnName = 'webinar_id';
                    $isOwner = $course->isOwner($user->id);

                    $checkUserHasBought = $course->checkUserHasBought($user);
                }
            } elseif (!empty($data['bundle_id'])) {

                $bundle = Bundle::find($data['bundle_id']);

                if (!empty($bundle)) {
                    $sellerId = $bundle->creator_id;
                    $itemId = $bundle->id;
                    $itemType = Sale::$bundle;
                    $itemColumnName = 'bundle_id';
                    $isOwner = $bundle->isOwner($user->id);

                    $checkUserHasBought = $bundle->checkUserHasBought($user);
                }
            } elseif (!empty($data['product_id'])) {

                $product = Product::find($data['product_id']);

                if (!empty($product)) {
                    $sellerId = $product->creator_id;
                    $itemId = $product->id;
                    $itemType = Sale::$product;
                    $itemColumnName = 'product_order_id';

                    $isOwner = ($product->creator_id == $user->id);

                    $checkUserHasBought = $product->checkUserHasBought($user);
                }
            }

            $errors = [];

            if ($isOwner) {
                $errors = [
                    'user_id' => [trans('cart.cant_purchase_your_course')],
                    'webinar_id' => [trans('cart.cant_purchase_your_course')],
                    'bundle_id' => [trans('cart.cant_purchase_your_course')],
                    'product_id' => [trans('update.cant_purchase_your_product')],
                ];
            }

            if ((empty($errors) or !count($errors)) and $checkUserHasBought) {
                $errors = [
                    'user_id' => [trans('site.you_bought_webinar')],
                    'webinar_id' => [trans('site.you_bought_webinar')],
                    'bundle_id' => [trans('update.you_bought_bundle')],
                    'product_id' => [trans('update.you_bought_product')],
                ];
            }

            if (!empty($errors) and count($errors)) {
                if ($request->ajax()) {
                    return response([
                        'code' => 422,
                        'errors' => $errors,
                    ], 422);
                } else {
                    return back()->withErrors($errors);
                }
            }

            if (!empty($itemType) and !empty($itemId) and !empty($itemColumnName) and !empty($sellerId)) {

                $productOrder = null;
                if (!empty($product)) {
                    $productOrder = ProductOrder::create([
                        'product_id' => $product->id,
                        'seller_id' => $product->creator_id,
                        'buyer_id' => $user->id,
                        'specifications' => null,
                        'quantity' => 1,
                        'status' => 'pending',
                        'created_at' => time()
                    ]);

                    $itemId = $productOrder->id;
                    $itemType = Sale::$product;
                    $itemColumnName = 'product_order_id';
                }

                $online_payment_method = (isset($data['online_payment_method']) && $data['online_payment_method'] && (isset($data['paid_amount']) && $data['paid_amount'])) ? $data['online_payment_method'] : 'تحويل مباشر';

                $sale = Sale::create([
                    'buyer_id' => $user->id,
                    'seller_id' => $sellerId,
                    $itemColumnName => $itemId,
                    'online_payment_method' => $online_payment_method,
                    'online_payment_method_id' => (isset($data['online_payment_method_id']) && $data['online_payment_method_id'] && (isset($data['paid_amount']) && $data['paid_amount'])) ? $data['online_payment_method_id'] : null,
                    'type' => $itemType,
                    'manual_added' => true,
                    'payment_method' => Sale::$credit,
                    'amount' => (isset($data['paid_amount']) && $data['paid_amount']) ? $data['paid_amount'] : 0,
                    'total_amount' => (isset($data['paid_amount']) && $data['paid_amount']) ? $data['paid_amount'] : 0,
                    'created_at' => time()
                ]);

                if (!empty($product) and !empty($productOrder)) {
                    $productOrder->update([
                        'sale_id' => $sale->id,
                        'status' => $product->isVirtual() ? ProductOrder::$success : ProductOrder::$waitingDelivery,
                    ]);
                }


                if ($request->ajax()) {
                    return response()->json([
                        'code' => 200
                    ]);
                } else {
                    $toastData = [
                        'title' => trans('public.request_success'),
                        'msg' => trans('webinars.success_store'),
                        'status' => 'success'
                    ];
                    return redirect()->back();
                }
            }
        }

        $errors = [
            'user_id' => [trans('update.something_went_wrong')],
            'webinar_id' => [trans('update.something_went_wrong')],
            'bundle_id' => [trans('update.something_went_wrong')],
            'product_id' => [trans('update.something_went_wrong')],
        ];

        if ($request->ajax()) {
            return response([
                'code' => 422,
                'errors' => $errors,
            ], 422);
        } else {
            return back()->withErrors($errors);
        }
    }



    public function addStudentViaExcel(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->all();

        if (!isset($data['excel_file'])) {
            return response()->json([
                'status' => 422,
                'message' => "Excel file is required",
                'reload' => false
            ], 422);
        }

        $file = public_path($data['excel_file']);

        if (!file_exists($file)) {
            return response()->json([
                'status' => 422,
                'message' => "File not found",
                'reload' => false
            ], 422);
        }

        $rules = [
            'user_id' => 'required|exists:users,id',
        ];

        try {
            $import = new WebinarSaleImport($data, $rules);
            Excel::import($import, $file);
//            dd($import->getResult());
//            $result = $import->getResult(); // Assuming you add a getResult() method to WebinarSaleImport
//
//            if ($result['error_count'] > 0) {
//                return response()->json([
//                    'status' => 207, // 207 Multi-Status for partial success
//                    'message' => "Import completed with some errors",
//                    'success_count' => $result['success_count'],
//                    'error_count' => $result['error_count'],
//                    'errors' => $result['errors'],
//                    'reload' => true
//                ]);
//            }


            return redirect()->back()->with('success');
            //            return response()->json([
//                'status' => 200,
//                'message' => "تمت العملية بنجاح",
////                'success_count' => $result['success_count'],
//                'reload' => true
//            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Error processing file: " . $e->getMessage(),
                'reload' => false
            ], 500);
        }
    }
}
