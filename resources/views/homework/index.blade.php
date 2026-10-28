@extends('layouts.app')
@section('title', 'Homework')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">ACADEMICS</p>
    <h1 class="page-title">Homework</h1>
    <p class="page-description">Assign homework, see who has submitted, and approve their work.</p>
  </div>
  <a href="{{ route('homework.create') }}" class="btn"><span aria-hidden="true">+</span> Assign homework</a>
</div>
<section class="table-panel">
  <div class="table-panel-heading"><div><h2>Assigned homework</h2><p>{{ $items->total() }} {{ \Illuminate\Support\Str::plural('assignment', $items->total()) }}</p></div></div>
  <div class="table-scroll">
    <table class="w-full">
      <thead><tr><th class="th">Title</th><th class="th">Class</th><th class="th">Subject</th><th class="th">Due</th><th class="th">Submitted</th><th class="th">To review</th><th class="th">Approved</th><th class="th text-right">Actions</th></tr></thead>
      <tbody>
      @forelse($items as $h)
        <tr>
          <td class="td font-medium text-slate-800">{{ $h->title }}</td>
          <td class="td">{{ $h->schoolClass->label }}</td>
          <td class="td">{{ $h->subject->name }}</td>
          <td class="td">{{ $h->due_date->format('d M Y') }}</td>
          <td class="td">{{ $h->submitted_count }} / {{ $totals[$h->school_class_id] ?? 0 }}</td>
          <td class="td">@if($h->review_count)<span class="pill pill-amber">{{ $h->review_count }} waiting</span>@else<span class="text-slate-400">—</span>@endif</td>
          <td class="td">{{ $h->approved_count }}</td>
          <td class="td row-actions">
            <a class="btn btn-sm" href="{{ route('homework.show', $h) }}">Open</a>
            <form method="POST" action="{{ route('homework.destroy', $h) }}" class="inline" onsubmit="return confirm('Delete this homework and all submissions?')">
              @csrf @method('DELETE')<button class="btn btn-sm btn-red">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="empty-cell" colspan="8">No homework yet. Click “Assign homework” to add the first one.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($items->hasPages())<div class="table-panel-footer">{{ $items->links() }}</div>@endif
</section>
@endsection
