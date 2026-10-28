<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Safe to run from the Teacher or Student app on a shared database (every table is guarded).
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('homework_files')) Schema::create('homework_files', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('mime')->nullable(); $t->unsignedInteger('size')->default(0);
            $t->longText('data'); // base64 - keeps files in the shared database so both apps can read them
            $t->timestamps();
        });
        if (!Schema::hasTable('homework')) Schema::create('homework', function (Blueprint $t) {
            $t->id();
            $t->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $t->string('title'); $t->text('description')->nullable(); $t->date('due_date');
            $t->foreignId('file_id')->nullable()->constrained('homework_files')->nullOnDelete();
            $t->timestamps();
        });
        if (!Schema::hasTable('homework_submissions')) Schema::create('homework_submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('homework_id')->constrained('homework')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->text('answer')->nullable();
            $t->foreignId('file_id')->nullable()->constrained('homework_files')->nullOnDelete();
            $t->timestamp('submitted_at')->nullable();
            $t->string('status')->default('pending'); // pending | approved | redo
            $t->text('remarks')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['homework_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homework');
        Schema::dropIfExists('homework_files');
    }
};
