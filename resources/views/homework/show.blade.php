@extends('layouts.app')
@section('title', 'Homework')
@section('content')
@php
  $submitted = $subs->count();
  $waiting = $subs->where('status', 'pending')->count();
  $approved = $subs->where('status', 'approved')->count();
@endphp
<div class="page-heading">
  <div>
    <p class="page-eyebrow">HOMEWORK</p>
    <h1 class="page-title">{{ $homework->title }}</h1>
    <p class="page-description">{{ $homework->schoolClass->label }} <span class="heading-separator">·</span> {{ $homework->subject->name }} <span class="heading-separator">·</span> Due {{ $homework->due_date->format('d M Y') }}</p>
  </div>
  <a href="{{ route('homework') }}" class="btn btn-quiet"><span aria-hidden="true">←</span> All homework</a>
</div>

@if($homework->description || $homework->file)
<div class="card mb-6">
  @if($homework->description)<p class="whitespace-pre-line text-sm text-slate-700">{{ $homework->description }}</p>@endif
  @if($homework->file)<p class="mt-3 text-sm"><a class="font-medium text-emerald-600 hover:text-emerald-800" href="{{ route('homework.file', $homework->file) }}">⬇ {{ $homework->file->name }}</a></p>@endif
</div>
@endif

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
  @foreach([
    ['Submitted', $submitted . ' / ' . $students->count(), 'SB', 'bg-emerald-50 text-emerald-600'],
    ['Waiting for review', $waiting, 'RV', 'bg-amber-50 text-amber-600'],
    ['Approved', $approved, 'OK', 'bg-sky-50 text-sky-600'],
  ] as [$label, $value, $icon, $tone])
    <div class="card flex items-start justify-between gap-3">
      <div><p class="text-xs font-medium text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $value }}</p></div>
      <span class="grid h-10 w-10 place-items-center rounded-xl text-xs font-bold {{ $tone }}">{{ $icon }}</span>
    </div>
  @endforeach
</div>

<section class="table-panel">
  <div class="table-panel-heading"><div><h2>Student submissions</h2><p>Review each submission, then approve it or send it back for redo.</p></div></div>
  <div class="table-scroll">
    <table class="w-full">
      <thead><tr><th class="th">Roll</th><th class="th">Student</th><th class="th">Status</th><th class="th">Work</th><th class="th">Review</th></tr></thead>
      <tbody>
      @forelse($students as $s)
        @php $sub = $subs[$s->id] ?? null; @endphp
        <tr>
          <td class="td">{{ $s->roll_no }}</td>
          <td class="td font-medium text-slate-800">{{ $s->user->name }}</td>
          <td class="td">
            @if(!$sub)
              <span class="pill {{ $homework->due_date->isPast() && !$homework->due_date->isToday() ? 'pill-red' : 'pill-gray' }}">Not submitted</span>
            @else
              <span class="pill {{ $sub->status === 'approved' ? 'pill-green' : ($sub->status === 'redo' ? 'pill-red' : 'pill-amber') }}">{{ $sub->status === 'pending' ? 'Submitted' : ($sub->status === 'redo' ? 'Redo' : 'Approved') }}</span>
              @if($sub->isLate())<span class="pill pill-red">Late</span>@endif
              <p class="mt-1 text-xs text-slate-500">{{ $sub->submitted_at?->format('d M, h:i A') }}</p>
            @endif
          </td>
          <td class="td">
            @if($sub)
              @if($sub->answer)<details><summary class="cursor-pointer text-sm font-medium text-slate-700">View answer</summary><p class="mt-2 max-w-md whitespace-pre-line text-sm text-slate-600">{{ $sub->answer }}</p></details>@endif
              @if($sub->file)<a class="text-sm font-medium text-emerald-600 hover:text-emerald-800" href="{{ route('homework.file', $sub->file) }}">⬇ {{ $sub->file->name }}</a>@endif
            @else <span class="text-slate-300">—</span> @endif
          </td>
          <td class="td">
            @if($sub)
              <form method="POST" action="{{ route('homework.review', [$homework, $sub]) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="text" name="remarks" value="{{ $sub->remarks }}" placeholder="Remarks (optional)" maxlength="1000" class="input" style="min-width: 11rem">
                <button class="btn btn-sm" type="submit" name="decision" value="approved">Approve</button>
                <button class="btn btn-sm btn-gray" type="submit" name="decision" value="redo">Redo</button>
              </form>
            @else <span class="text-slate-300">—</span> @endif
          </td>
        </tr>
      @empty
        <tr><td class="empty-cell" colspan="5">No students in this class.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
