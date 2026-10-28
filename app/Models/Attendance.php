<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];
    protected $casts = ['date' => 'date'];
    public function student() { return $this->belongsTo(Student::class); }
    public function marker() { return $this->belongsTo(User::class, 'marked_by'); }
}
