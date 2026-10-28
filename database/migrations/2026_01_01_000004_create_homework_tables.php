<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Safe to run from every School ERP app that shares the database.
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('homework_files')) Schema::create('homework_files', function (Blueprint $t) {
            // Files live in the (shared) database so teacher and student apps can both open them.
            $t->id(); $t->string('name'); $t->string('mime')->nullable(); $t->unsignedInteger('size')->default(0);
            $t->longText('data'); // base64
            $t->timestamps();
        });
        if (!Schema::hasTable('homeworks')) Schema::create('homeworks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->date('due_date');
            $t->unsignedSmallInteger('max_marks')->default(10);
            $t->unsignedBigInteger('attachment_id')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('homework_submissions')) Schema::create('homework_submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('homework_id')->constrained('homeworks')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->text('answer')->nullable();
            $t->unsignedBigInteger('file_id')->nullable();
            $t->string('status')->default('submitted'); // submitted | approved | rejected
            $t->timestamp('submitted_at')->nullable();
            $t->decimal('marks', 5, 2)->nullable();
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
        Schema::dropIfExists('homeworks');
        Schema::dropIfExists('homework_files');
    }
};
