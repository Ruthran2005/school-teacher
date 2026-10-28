<?php
namespace App\Http\Controllers;

use App\Models\{Exam, Student};
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $r)
    {
        $exams = Exam::with('schoolClass')->whereIn('school_class_id', $r->user()->teachingClassIds())->latest('id')->get();
        return view('exams.index', compact('exams'));
    }

    public function results(Request $r, $id)
    {
        $exam = Exam::with('schoolClass')->findOrFail($id);
        abort_unless(in_array($exam->school_class_id, $r->user()->teachingClassIds(), true), 403, 'You are not assigned to this class.');
        $students = Student::with('user')->where('school_class_id', $exam->school_class_id)->orderBy('roll_no')->get();
        return view('exams.results', compact('exam', 'students'));
    }
}
