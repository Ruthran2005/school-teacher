<?php
namespace App\Http\Controllers;

use App\Models\{Exam, Mark, Student};

class ReportController extends Controller
{
    public function card(Student $student, Exam $exam)
    {
        abort_unless(in_array($student->school_class_id, auth()->user()->teachingClassIds(), true), 403, 'You are not assigned to this class.');
        $student->load(['user', 'schoolClass']);
        $marks = Mark::with('subject')->where('exam_id', $exam->id)->where('student_id', $student->id)->get();
        $total = $marks->sum('marks_obtained');
        $max = $marks->sum('max_marks');
        $pct = $max ? round($total / $max * 100, 1) : 0;
        $grade = Mark::grade($pct);
        $attendance = $student->attendancePercent();
        return view('report.card', compact('student', 'exam', 'marks', 'total', 'max', 'pct', 'grade', 'attendance'));
    }
}
