<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HomeworkSubmission extends Model
{
    protected $guarded = [];
    protected $casts = ['submitted_at' => 'datetime', 'reviewed_at' => 'datetime'];

    public function homework() { return $this->belongsTo(Homework::class, 'homework_id'); }
    public function student() { return $this->belongsTo(Student::class); }
    public function file() { return $this->belongsTo(HomeworkFile::class, 'file_id'); }
    public function isLate(): bool
    {
        return $this->submitted_at && $this->homework && $this->submitted_at->toDateString() > $this->homework->due_date->toDateString();
    }
}
