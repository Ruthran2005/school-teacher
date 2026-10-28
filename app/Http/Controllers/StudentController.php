<?php
namespace App\Http\Controllers;

use App\Models\{SchoolClass, Student};
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $r)
    {
        $classes = SchoolClass::whereIn('id', $r->user()->teachingClassIds())->orderBy('name')->orderBy('section')->get();
        $class = $r->class ? $classes->firstWhere('id', (int) $r->class) : null;
        $students = Student::with(['user', 'schoolClass'])
            ->whereIn('school_class_id', $class ? [$class->id] : $classes->pluck('id')->all())
            ->withCount([
                'attendances as total_days',
                'attendances as present_days' => fn ($q) => $q->whereIn('status', ['present', 'late']),
            ])
            ->orderBy('school_class_id')->orderBy('roll_no')->paginate(25)->withQueryString();
        return view('students.index', compact('classes', 'class', 'students'));
    }
}
