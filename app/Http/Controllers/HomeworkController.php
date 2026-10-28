<?php
namespace App\Http\Controllers;

use App\Models\{Homework, HomeworkFile, HomeworkSubmission, SchoolClass, Student, Subject, Timetable};
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    private function own(Request $r, Homework $homework): void
    {
        abort_unless((int) $homework->teacher_id === (int) $r->user()->id, 403, 'This homework belongs to another teacher.');
    }

    private function options($user): array
    {
        $classes = SchoolClass::whereIn('id', $user->teachingClassIds())->orderBy('name')->orderBy('section')->get();
        $subjects = Subject::whereIn('id', Timetable::where('teacher_id', $user->id)->pluck('subject_id')->unique()->all())->orderBy('name')->get();
        return [$classes, $subjects];
    }

    public function index(Request $r)
    {
        $items = Homework::with(['schoolClass', 'subject'])
            ->withCount([
                'submissions as submitted_count',
                'submissions as review_count' => fn ($q) => $q->where('status', 'pending'),
                'submissions as approved_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->where('teacher_id', $r->user()->id)->latest('id')->paginate(15);
        $totals = Student::whereIn('school_class_id', $items->pluck('school_class_id')->unique()->all())
            ->selectRaw('school_class_id, count(*) as c')->groupBy('school_class_id')->pluck('c', 'school_class_id');
        return view('homework.index', compact('items', 'totals'));
    }

    public function create(Request $r)
    {
        [$classes, $subjects] = $this->options($r->user());
        return view('homework.create', compact('classes', 'subjects'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'due_date' => 'required|date|after_or_equal:today',
            'file' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx,txt',
        ]);
        abort_unless(in_array((int) $data['school_class_id'], $r->user()->teachingClassIds(), true), 403, 'You are not assigned to this class.');

        $file = $r->file('file') ? HomeworkFile::fromUpload($r->file('file')) : null;
        $homework = Homework::create([
            'school_class_id' => $data['school_class_id'], 'subject_id' => $data['subject_id'], 'teacher_id' => $r->user()->id,
            'title' => $data['title'], 'description' => $data['description'] ?? null, 'due_date' => $data['due_date'],
            'file_id' => $file?->id,
        ]);
        return redirect()->route('homework.show', $homework)->with('ok', 'Homework assigned to the class.');
    }

    public function show(Request $r, Homework $homework)
    {
        $this->own($r, $homework);
        $homework->load(['schoolClass', 'subject', 'file']);
        $students = Student::with('user')->where('school_class_id', $homework->school_class_id)->orderBy('roll_no')->get();
        $subs = $homework->submissions()->with('file')->get()->keyBy('student_id');
        return view('homework.show', compact('homework', 'students', 'subs'));
    }

    public function review(Request $r, Homework $homework, HomeworkSubmission $submission)
    {
        $this->own($r, $homework);
        abort_unless((int) $submission->homework_id === (int) $homework->id, 404);
        $data = $r->validate(['decision' => 'required|in:approved,redo', 'remarks' => 'nullable|string|max:1000']);
        $submission->update([
            'status' => $data['decision'], 'remarks' => $data['remarks'] ?? null,
            'reviewed_at' => now(), 'reviewed_by' => $r->user()->id,
        ]);
        return back()->with('ok', $data['decision'] === 'approved' ? 'Submission approved.' : 'Sent back to the student for redo.');
    }

    public function destroy(Request $r, Homework $homework)
    {
        $this->own($r, $homework);
        $fileIds = $homework->submissions()->pluck('file_id')->push($homework->file_id)->filter()->all();
        $homework->delete();
        HomeworkFile::whereIn('id', $fileIds)->delete();
        return redirect()->route('homework')->with('ok', 'Homework deleted.');
    }

    public function file(Request $r, HomeworkFile $file)
    {
        $uid = $r->user()->id;
        $ok = Homework::where('teacher_id', $uid)->where('file_id', $file->id)->exists()
            || HomeworkSubmission::where('file_id', $file->id)->whereHas('homework', fn ($q) => $q->where('teacher_id', $uid))->exists();
        abort_unless($ok, 403, 'You cannot open this file.');
        return $file->download();
    }
}
