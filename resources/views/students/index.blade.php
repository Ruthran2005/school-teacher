@extends('layouts.app')
@section('title', 'My students')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">MY CLASSES</p>
    <h1 class="page-title">My students</h1>
    <p class="page-description">Students in the classes you teach, with their attendance so far.</p>
  </div>
</div>
@if($classes->isEmpty())
  <div class="flash flash-error" role="status">You are not assigned to any class yet. Ask the school admin to add you to the timetable.</div>
@endif
<form method="GET" class="filter-panel">
  <div class="filter-field"><label for="students-class">Class</label>
    <select id="students-class" name="class" class="input"><option value="">All my classes</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected($class && $class->id === $c->id)>{{ $c->label }}</option>@endforeach
    </select></div>
  <button class="btn" type="submit">Filter <span aria-hidden="true">→</span></button>
</form>
<section class="table-panel">
  <div class="table-panel-heading">
    <div><h2>Student list</h2><p>{{ $students->total() }} {{ \Illuminate\Support\Str::plural('student', $students->total()) }}</p></div>
  </div>
  <div class="table-scroll">
    <table class="w-full">
      <thead><tr><th class="th">Roll</th><th class="th">Name</th><th class="th">Class</th><th class="th">Admission no</th><th class="th">Attendance</th></tr></thead>
      <tbody>
      @forelse($students as $s)
        @php $pct = $s->total_days ? round($s->present_days / $s->total_days * 100) : null; @endphp
        <tr>
          <td class="td">{{ $s->roll_no }}</td>
          <td class="td font-medium text-slate-800">{{ $s->user->name }}</td>
          <td class="td">{{ $s->schoolClass->label }}</td>
          <td class="td">{{ $s->admission_no }}</td>
          <td class="td">
            @if($pct === null)<span class="text-slate-400">No records</span>
            @else<span class="pill {{ $pct >= 85 ? 'pill-green' : ($pct >= 75 ? 'pill-amber' : 'pill-red') }}">{{ $pct }}%</span>@endif
          </td>
        </tr>
      @empty
        <tr><td class="empty-cell" colspan="5">No students found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($students->hasPages())<div class="table-panel-footer">{{ $students->links() }}</div>@endif
</section>
@endsection
