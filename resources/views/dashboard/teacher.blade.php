@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
  <div>
    <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-emerald-600">Overview</p>
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h1>
    <p class="mt-1 text-sm text-slate-500">Here’s your teaching day at a glance.</p>
  </div>
  <p class="text-xs font-medium text-slate-500">{{ now()->format('l, d F Y') }}</p>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  @foreach([
    ['My classes', $stats['classes'], 'CL', 'bg-emerald-50 text-emerald-600'],
    ['My students', $stats['students'], 'ST', 'bg-sky-50 text-sky-600'],
    ['Periods today', $stats['periods'], 'PD', 'bg-violet-50 text-violet-600'],
    ['Attendance marked today', $stats['marked'] . ' / ' . $stats['classes'], 'AT', 'bg-amber-50 text-amber-600'],
  ] as [$label, $value, $icon, $tone])
    <div class="card flex items-start justify-between gap-3">
      <div>
        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
      </div>
      <span class="grid h-10 w-10 place-items-center rounded-xl text-xs font-bold {{ $tone }}">{{ $icon }}</span>
    </div>
  @endforeach
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
  <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
      <div>
        <h2 class="text-sm font-semibold text-slate-900">Today’s schedule</h2>
        <p class="mt-1 text-xs text-slate-500">Your periods for {{ now()->format('l') }}</p>
      </div>
      <a href="{{ route('timetable') }}" class="btn btn-quiet btn-sm">Full timetable <span aria-hidden="true">→</span></a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead><tr><th class="th">Period</th><th class="th">Time</th><th class="th">Class</th><th class="th">Subject</th></tr></thead>
        <tbody>
        @forelse($periods as $p)
          <tr>
            <td class="td font-medium text-slate-800">P{{ $p->period }}</td>
            <td class="td">{{ substr($p->start_time, 0, 5) }}–{{ substr($p->end_time, 0, 5) }}</td>
            <td class="td">{{ $p->schoolClass->label }}</td>
            <td class="td font-medium text-slate-800">{{ $p->subject->name }}</td>
          </tr>
        @empty
          <tr><td class="td py-10 text-center text-slate-500" colspan="4">No periods scheduled for today.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="space-y-6">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-slate-900">Quick actions</h2>
        <p class="mt-1 text-xs text-slate-500">Jump straight to your daily tasks</p>
      </div>
      <div class="panel-body quick-actions">
        <a href="{{ route('staff.attendance') }}" class="btn btn-sm">Take attendance</a>
        <a href="{{ route('staff.marks') }}" class="btn btn-sm btn-gray">Enter marks</a>
        <a href="{{ route('students') }}" class="btn btn-sm btn-gray">My students</a>
      </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-slate-900">Upcoming exams</h2>
        <p class="mt-1 text-xs text-slate-500">For the classes you teach</p>
      </div>
      <ul class="divide-y divide-slate-100">
        @forelse($upcoming as $e)
          <li class="flex items-center justify-between gap-3 px-5 py-3">
            <div><p class="text-sm font-medium text-slate-800">{{ $e->name }}</p><p class="text-xs text-slate-500">{{ $e->schoolClass->label }}</p></div>
            <span class="pill pill-gray">{{ $e->start_date ? $e->start_date->format('d M') : 'TBA' }}</span>
          </li>
        @empty
          <li class="px-5 py-8 text-center text-sm text-slate-500">No upcoming exams.</li>
        @endforelse
      </ul>
    </section>
  </div>
</div>
@endsection
