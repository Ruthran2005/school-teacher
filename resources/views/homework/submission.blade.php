@extends('layouts.app')
@section('title', 'Review homework')
@section('content')
@php $submission->setRelation('homework', $homework); @endphp
<div class="page-heading">
  <div>
    <p class="page-eyebrow">REVIEW SUBMISSION</p>
    <h1 class="page-title">{{ $submission->student->user->name }}</h1>
    <p class="page-description">{{ $homework->title }} · {{ $homework->subject->name }} · submitted {{ $submission->submitted_at->format('d M Y, H:i') }}@if($submission->isLate()) <span class="pill pill-red">Late</span>@endif</p>
  </div>
  <a href="{{ route('homework.show', $homework) }}" class="btn btn-quiet">← Back to list</a>
</div>

<section class="card mb-6">
  <h2 class="mb-2 text-sm font-semibold text-slate-900">Student’s work</h2>
  @if($submission->answer)<p class="whitespace-pre-line text-sm text-slate-700">{{ $submission->answer }}</p>@endif
  @if($submission->file)<p class="mt-3 text-sm"><a class="font-medium text-emerald-600 hover:text-emerald-800" href="{{ route('homework.file', $submission->file) }}">⬇ {{ $submission->file->name }}</a></p>@endif
  @if(!$submission->answer && !$submission->file)<p class="text-sm text-slate-500">Nothing was attached.</p>@endif
</section>

<form method="POST" action="{{ route('homework.review', [$homework, $submission]) }}" class="form-panel">
  @csrf
  <div class="form-panel-heading"><h2>Your decision</h2>
    <p>Current status: <span class="pill {{ \App\Models\HomeworkSubmission::PILLS[$submission->status] }}">{{ \App\Models\HomeworkSubmission::LABELS[$submission->status] }}</span></p></div>
  <div class="form-grid">
    <div class="form-field form-field-wide">
      <p class="form-field-label">Decision</p>
      <div class="permission-grid">
        <label class="permission-option" for="dec-ok"><input id="dec-ok" type="radio" name="decision" value="approved" @checked(old('decision', $submission->status === 'rejected' ? 'rejected' : 'approved') === 'approved')><span>Approve</span></label>
        <label class="permission-option" for="dec-no"><input id="dec-no" type="radio" name="decision" value="rejected" @checked(old('decision', $submission->status) === 'rejected')><span>Needs rework</span></label>
      </div>@error('decision')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-field"><label for="hw-marks">Marks (out of {{ $homework->max_marks }})</label>
      <input id="hw-marks" type="number" step="any" min="0" max="{{ $homework->max_marks }}" name="marks" class="input" value="{{ old('marks', $submission->marks) }}">@error('marks')<p class="field-error">{{ $message }}</p>@enderror</div>
    <div class="form-field form-field-wide"><label for="hw-remarks">Remarks <span class="field-hint">(required for rework)</span></label>
      <textarea id="hw-remarks" name="remarks" rows="3" class="input">{{ old('remarks', $submission->remarks) }}</textarea>@error('remarks')<p class="field-error">{{ $message }}</p>@enderror</div>
  </div>
  <div class="form-panel-footer">
    <a href="{{ route('homework.show', $homework) }}" class="btn btn-quiet">Cancel</a>
    <button class="btn" type="submit"><span aria-hidden="true">✓</span> Save decision</button>
  </div>
</form>
@endsection
