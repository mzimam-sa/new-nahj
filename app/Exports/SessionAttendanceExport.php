<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SessionAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $session;
    protected $students;
    protected $presentStudents;

    public function __construct($session, $students, $presentStudents)
    {
        $this->session = $session;
        $this->students = $students;
        $this->presentStudents = $presentStudents;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Email',
            'Attended',
        ];
    }

    public function map($student): array
    {
        return [
            $student->full_name,
            $student->email,
            in_array($student->id, $this->presentStudents) ? 'Yes' : 'No',
        ];
    }
}
