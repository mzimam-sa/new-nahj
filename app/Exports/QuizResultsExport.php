<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QuizResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->results;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/pages/quiz.title'),
            'المحتوى التعليمي',
            trans('quiz.student'),
            'درجة الاختبار',
            'درجة المتدرب',
            trans('admin/pages/quiz.grade_date'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($result): array
    {
        $quizTotalGrade = $result->quiz->quizQuestions ? $result->quiz->quizQuestions->sum('grade') : 0;
        $studentGrade   = $result->user_grade;

        $statusMap = [
            'passed'  => 'ناجح',
            'failed'  => 'راسب',
            'waiting' => 'قيد المراجعة',
        ];

        return [
            $result->id,
            $result->quiz->title,
            optional($result->quiz->webinar)->title,
            $result->user->full_name,
            $quizTotalGrade == 0 ? 'صفر' : $quizTotalGrade,
            $studentGrade == 0 ? 'صفر' : $studentGrade,
            dateTimeformat($result->created_at, 'j F Y'),
            $statusMap[$result->status] ?? $result->status,
        ];
    }
}
