<?php
namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $r)
    {
        $slots = Timetable::with(['schoolClass', 'subject'])->where('teacher_id', $r->user()->id)
            ->orderBy('day')->orderBy('period')->get();
        $periods = $slots->pluck('period')->unique()->sort()->values();
        return view('timetable.week', ['slots' => $slots, 'periods' => $periods, 'mode' => 'teacher', 'class' => null]);
    }
}
