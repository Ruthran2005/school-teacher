<?php
namespace App\Http\Controllers;

use App\Models\{Attendance, SchoolClass, Student};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $r)
    {
        $classes = SchoolClass::whereIn('id', $r->user()->teachingClassIds())->orderBy('name')->orderBy('section')->get();
        $date = $r->date ?: today()->toDateString();
        $class = $r->class ? $classes->firstWhere('id', (int) $r->class) : null;
        $students = $class ? Student::with('user')->where('school_class_id', $class->id)->orderBy('roll_no')->get() : collect();
        $existing = $class ? Attendance::where('school_class_id', $class->id)->whereDate('date', $date)->pluck('status', 'student_id') : collect();
        return view('attendance.index', compact('classes', 'class', 'date', 'students', 'existing'));
    }

    public function store(Request $r)
    {
        $r->validate(['class' => 'required|exists:school_classes,id', 'date' => 'required|date|before_or_equal:today',
            'status' => 'required|array', 'status.*' => 'in:present,absent,late']);
        abort_unless(in_array((int) $r->class, $r->user()->teachingClassIds(), true), 403, 'You are not assigned to this class.');

        $ids = Student::where('school_class_id', $r->class)->pluck('id')->all();
        foreach ($r->status as $studentId => $status) {
            if (!in_array((int) $studentId, $ids, true)) continue;
            $values = ['school_class_id' => $r->class, 'status' => $status, 'marked_by' => $r->user()->id];
            $existing = Attendance::where('student_id', $studentId)->whereDate('date', $r->date);
            if ($existing->exists()) {
                $existing->update($values);
            } else {
                Attendance::create($values + ['student_id' => $studentId, 'date' => $r->date]);
            }
        }
        return redirect()->route('staff.attendance', ['class' => $r->class, 'date' => $r->date])->with('ok', 'Attendance saved.');
    }
}
