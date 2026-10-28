<?php
use App\Http\Controllers\{AttendanceController, AuthController, DashboardController, ExamController, HomeworkController, MarksController,
    ReportController, StudentController, TimetableController};
use App\Http\Middleware\RoleMiddleware as Role;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth', Role::class . ':teacher'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('password', [AuthController::class, 'showPassword'])->name('password.edit');
    Route::put('password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('timetable', [TimetableController::class, 'index'])->name('timetable');
    Route::get('my-students', [StudentController::class, 'index'])->name('students');
    Route::get('exams', [ExamController::class, 'index'])->name('exams');
    Route::get('exams/{exam}/results', [ExamController::class, 'results'])->name('exams.results');
    Route::get('report/{student}/{exam}', [ReportController::class, 'card'])->name('report.card');

    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance');
        Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('marks', [MarksController::class, 'index'])->name('marks');
        Route::post('marks', [MarksController::class, 'store'])->name('marks.store');
    });
});

Route::middleware(['auth', Role::class . ':teacher'])->group(function () {
    Route::get('homework', [HomeworkController::class, 'index'])->name('homework');
    Route::get('homework/create', [HomeworkController::class, 'create'])->name('homework.create');
    Route::post('homework', [HomeworkController::class, 'store'])->name('homework.store');
    Route::get('homework/{homework}', [HomeworkController::class, 'show'])->name('homework.show');
    Route::delete('homework/{homework}', [HomeworkController::class, 'destroy'])->name('homework.destroy');
    Route::post('homework/{homework}/review/{submission}', [HomeworkController::class, 'review'])->name('homework.review');
    Route::get('homework-files/{file}', [HomeworkController::class, 'file'])->name('homework.file');
});
