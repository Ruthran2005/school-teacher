@extends('layouts.app')
@section('title', 'Assign homework')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">NEW HOMEWORK</p>
    <h1 class="page-title">Assign homework</h1>
    <p class="page-description">Students of the selected class will see it in their Homework tab.</p>
  </div>
  <a href="{{ route('homework') }}" class="btn btn-quiet"><span aria-hidden="true">←</span> Back</a>
</div>
@if($classes->isEmpty())
  <div class="flash flash-error" role="status">You are not assigned to any class yet. Ask the school admin to add you to the timetable.</div>
@endif
<form method="POST" action="{{ route('homework.store') }}" enctype="multipart/form-data" class="form-panel">
  @csrf
  <div class="form-panel-heading"><h2>Details</h2><p>Add a clear title, instructions and a due date. An attachment is optional (PDF, image, Word or text, up to 5 MB).</p></div>
  <div class="form-grid">
    <div class="form-field">
      <label for="hw-class">Class</label>
      <select id="hw-class" name="school_class_id" class="input" required><option value="">Select class</option>
        @foreach($classes as $c)<option value="{{ $c->id }}" @selected(old('school_class_id') == $c->id)>{{ $c->label }}</option>@endforeach
      </select>
      @error('school_class_id')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-field">
      <label for="hw-subject">Subject</label>
      <select id="hw-subject" name="subject_id" class="input" required><option value="">Select subject</option>
        @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(old('subject_id') == $s->id)>{{ $s->name }}</option>@endforeach
      </select>
      @error('subject_id')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-field form-field-wide">
      <label for="hw-title">Title</label>
      <input id="hw-title" type="text" name="title" value="{{ old('title') }}" class="input" maxlength="150" required>
      @error('title')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-field form-field-wide">
      <label for="hw-desc">Instructions</label>
      <textarea id="hw-desc" name="description" rows="5" class="input" maxlength="5000">{{ old('description') }}</textarea>
      @error('description')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-field">
      <label for="hw-due">Due date</label>
      <input id="hw-due" type="date" name="due_date" value="{{ old('due_date', now()->addDays(3)->toDateString()) }}" min="{{ today()->toDateString() }}" class="input" required>
      @error('due_date')<p class="field-error">{{ $message }}</p>@enderror
    </div>
    <div class="form-field">
      <label for="hw-file">Attachment <span class="field-hint">(optional)</span></label>
      <input id="hw-file" type="file" name="file" class="input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.txt">
      @error('file')<p class="field-error">{{ $message }}</p>@enderror
    </div>
  </div>
  <div class="form-panel-footer">
    <a href="{{ route('homework') }}" class="btn btn-quiet">Cancel</a>
    <button class="btn" type="submit"><span aria-hidden="true">✓</span> Assign homework</button>
  </div>
</form>
@endsection
