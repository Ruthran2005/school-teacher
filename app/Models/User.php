<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ADMIN_MODULES = [
        'students' => 'Students',
        'teachers' => 'Teachers',
        'classes' => 'Classes',
        'subjects' => 'Subjects',
        'timetable' => 'Timetable',
        'attendance' => 'Attendance',
        'exams' => 'Exams & reports',
        'marks' => 'Marks',
        'fees' => 'Fee structures',
        'invoices' => 'Invoices & receipts',
    ];

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'admin_permissions'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'admin_permissions' => 'array'];
    }

    public function student() { return $this->hasOne(Student::class); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isStudent(): bool { return $this->role === 'student'; }

    /** IDs of the classes this teacher is scheduled to teach (from the timetable). */
    public function teachingClassIds(): array
    {
        return Timetable::where('teacher_id', $this->id)->distinct()->pluck('school_class_id')->all();
    }

    public function hasModuleAccess(string $module): bool
    {
        return $this->isAdmin()
            && ($this->admin_permissions === null || in_array($module, $this->admin_permissions, true));
    }

    public function canManageAdmins(): bool
    {
        return $this->isAdmin() && $this->admin_permissions === null;
    }
}
