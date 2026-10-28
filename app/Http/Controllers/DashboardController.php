<?php
namespace App\Http\Controllers;

use App\Models\{Attendance, Exam, Student, Timetable};

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $classIds = $user->teachingClassIds();
        $today = today();

        $periods = Timetable::with(['schoolClass', 'subject'])
            ->where('teacher_id', $user->id)->where('day', $today->dayOfWeekIso)
            ->orderBy('period')->get();

        $upcoming = Exam::with('schoolClass')->whereIn('school_class_id', $classIds)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhereDate('start_date', '>=', $today->toDateString()))
            ->orderBy('start_date')->take(5)->get();

        $stats = [
            'classes' => count($classIds),
            'students' => Student::whereIn('school_class_id', $classIds)->count(),
            'periods' => $periods->count(),
            'marked' => Attendance::whereDate('date', $today->toDateString())->whereIn('school_class_id', $classIds)->distinct()->count('school_class_id'),
        ];

        return view('dashboard.teacher', compact('stats', 'periods', 'upcoming'));
    }
}
