@extends('layouts.app')
@section('title', 'Exams')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">ACADEMICS</p>
    <h1 class="page-title">Exams</h1>
    <p class="page-description">Exams for your classes. Open one to view student report cards.</p>
  </div>
  <a href="{{ route('staff.marks') }}" class="btn">Enter marks <span aria-hidden="true">→</span></a>
</div>
<section class="table-panel">
  <div class="table-panel-heading"><div><h2>Exam list</h2><p>{{ $exams->count() }} {{ \Illuminate\Support\Str::plural('exam', $exams->count()) }}</p></div></div>
  <div class="table-scroll">
    <table class="w-full">
      <thead><tr><th class="th">Exam</th><th class="th">Class</th><th class="th">Starts</th><th class="th text-right">Report cards</th></tr></thead>
      <tbody>
      @forelse($exams as $e)
        <tr>
          <td class="td font-medium text-slate-800">{{ $e->name }}</td>
          <td class="td">{{ $e->schoolClass->label }}</td>
          <td class="td">{{ $e->start_date ? $e->start_date->format('d M Y') : '—' }}</td>
          <td class="td row-actions"><a class="btn btn-sm btn-gray" href="{{ route('exams.results', $e->id) }}">View</a></td>
        </tr>
      @empty
        <tr><td class="empty-cell" colspan="4">No exams for your classes yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
