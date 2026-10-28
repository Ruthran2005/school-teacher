@extends('layouts.app')
@section('title', 'Timetable')
@section('content')
@php $today = now()->dayOfWeekIso; @endphp
<div class="page-heading">
  <div>
    <p class="page-eyebrow">{{ $mode === 'teacher' ? 'MY CLASSES' : 'MY SCHOOL' }}</p>
    <h1 class="page-title">Timetable</h1>
    <p class="page-description">{{ $mode === 'teacher' ? 'Your weekly teaching schedule.' : 'Your weekly class schedule' . ($class ? ' for ' . $class->label . '.' : '.') }}</p>
  </div>
</div>
<section class="table-panel">
  <div class="table-panel-heading">
    <div><h2>Weekly schedule</h2><p>{{ $slots->count() }} {{ \Illuminate\Support\Str::plural('period', $slots->count()) }} per week</p></div>
  </div>
  <div class="table-scroll">
    <table class="w-full">
      <thead>
        <tr>
          <th class="th">Period</th>
          @foreach(\App\Models\Timetable::DAYS as $n => $day)
            <th class="th {{ $n === $today ? 'tt-today' : '' }}">{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
      @forelse($periods as $p)
        @php $first = $slots->firstWhere('period', $p); @endphp
        <tr>
          <td class="td"><strong>P{{ $p }}</strong><br><small class="text-slate-500">{{ substr($first->start_time, 0, 5) }}–{{ substr($first->end_time, 0, 5) }}</small></td>
          @foreach(\App\Models\Timetable::DAYS as $n => $day)
            @php $cell = $slots->first(fn ($s) => $s->period == $p && $s->day == $n); @endphp
            <td class="td">
              @if($cell)
                <div @class(['tt-cell', 'is-today' => $n === $today])><strong>{{ $cell->subject->name }}</strong><span>{{ $mode === 'teacher' ? $cell->schoolClass->label : ($cell->teacher->name ?? '—') }}</span></div>
              @else
                <span class="text-slate-300">—</span>
              @endif
            </td>
          @endforeach
        </tr>
      @empty
        <tr><td class="empty-cell" colspan="7">No periods are scheduled yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
