<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['student_id', 'assignment_id', 'student_answer'];

    protected $casts = [
        'student_answer' => 'json', // Mengonversi JSON ke array otomatis
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function result()
    {
        return $this->belongsTo(Result::class, 'student_id', 'student_id')
                    ->whereColumn('assignment_id', 'assignment_id');
    }
}
