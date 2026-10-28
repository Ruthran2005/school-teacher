<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homework';
    protected $guarded = [];
    protected $casts = ['due_date' => 'date'];

    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
    public function file() { return $this->belongsTo(HomeworkFile::class, 'file_id'); }
    public function submissions() { return $this->hasMany(HomeworkSubmission::class, 'homework_id'); }
}
