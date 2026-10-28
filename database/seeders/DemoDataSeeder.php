<?php
namespace Database\Seeders;

use App\Models\{Attendance, Exam, FeeInvoice, Mark, Payment, SchoolClass, Student, Subject, User};
use Illuminate\Database\Seeder;

// Optional demo activity (classmates, 20 days of attendance, one exam with marks, one part-payment).
// Safe to run more than once; needs the base DatabaseSeeder data. Fake data only.
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@school.test')->first();
        $class = SchoolClass::where('name', 'Class 6')->first();
        if (!$teacher || !$class || Mark::exists()) return;

        foreach (['Divya Suresh', 'Karthik Mohan', 'Meena Raj', 'Surya Kumar', 'Nila Prakash'] as $i => $name) {
            $user = User::firstOrCreate(
                ['email' => strtolower(strtok($name, ' ')) . '@school.test'],
                ['name' => $name, 'password' => 'password', 'role' => 'student']
            );
            Student::firstOrCreate(['user_id' => $user->id], [
                'school_class_id' => $class->id, 'admission_no' => 'ADM00' . ($i + 2),
                'roll_no' => $i + 2, 'gender' => $i % 2 ? 'Male' : 'Female',
            ]);
        }

        $students = Student::where('school_class_id', $class->id)->get();
        $subjects = Subject::orderBy('id')->get();

        $day = today();
        for ($done = 0; $done < 20;) {
            $day = $day->copy()->subDay();
            if ($day->isWeekend()) continue;
            $done++;
            foreach ($students as $s) {
                $roll = mt_rand(1, 100);
                Attendance::updateOrCreate(
                    ['student_id' => $s->id, 'date' => $day->toDateString()],
                    ['school_class_id' => $class->id, 'status' => $roll <= 86 ? 'present' : ($roll <= 94 ? 'late' : 'absent'), 'marked_by' => $teacher->id]
                );
            }
        }

        $exam = Exam::firstOrCreate(['name' => 'Unit Test 1', 'school_class_id' => $class->id], ['start_date' => now()->subWeeks(3)->toDateString()]);
        foreach ($students as $s) {
            foreach ($subjects as $sub) {
                Mark::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $s->id, 'subject_id' => $sub->id],
                    ['marks_obtained' => mt_rand(48, 98), 'max_marks' => 100]
                );
            }
        }

        $arun = Student::where('admission_no', 'ADM001')->first();
        $invoice = $arun ? FeeInvoice::where('student_id', $arun->id)->first() : null;
        if ($invoice && !$invoice->payments()->exists()) {
            Payment::create([
                'fee_invoice_id' => $invoice->id, 'amount' => 5000, 'method' => 'UPI',
                'paid_on' => now()->subDays(4)->toDateString(), 'receipt_no' => 'RCT-DEMO-00001',
                'received_by' => User::where('role', 'admin')->value('id'),
            ]);
            $invoice->refreshStatus();
        }
    }
}
