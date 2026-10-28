@extends('layouts.app')
@section('title', 'Report cards')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">ACADEMICS</p>
    <h1 class="page-title">Report cards</h1>
    <p class="page-description">{{ $exam->name }} <span class="heading-separator">·</span> {{ $exam->schoolClass->label }}</p>
  </div>
  <a href="{{ route('exams') }}" class="btn btn-quiet">← All exams</a>
</div>
<section class="table-panel">
  <div class="table-scroll">
    <table class="w-full">
      <thead><tr><th class="th">Roll</th><th class="th">Name</th><th class="th text-right">Report card</th></tr></thead>
      <tbody>
      @forelse($students as $s)
        <tr>
          <td class="td">{{ $s->roll_no }}</td>
          <td class="td font-medium text-slate-800">{{ $s->user->name }}</td>
          <td class="td row-actions"><a class="btn btn-sm" href="{{ route('report.card', [$s, $exam]) }}">View report card</a></td>
        </tr>
      @empty
        <tr><td class="empty-cell" colspan="3">No students in this class.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
