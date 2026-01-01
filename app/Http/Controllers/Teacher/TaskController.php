<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Course;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(SchoolClass $class, Course $course)
    {
        $teacher = Auth::user()->teacher;

        abort_if(!$teacher, 403);

        // WALI KELAS → hanya kelas walian
        if ($teacher->type === 'classroomTeacher') {
            abort_if($class->teacher_id !== $teacher->id, 403);
        }

        // Pastikan course milik kelas tsb
        abort_if(
            !$course->schoolClasses()
                ->where('school_classes.id', $class->id)
                ->exists(),
            404
        );

        $title = 'Daftar Tugas dan Materi';

        $materials = $course->materials()
            ->orderBy('order_number')
            ->get();

        $assignments = $course->assignments()
            ->where('school_class_id', $class->id)
            ->withCount('questions')
            ->orderBy('order_number')
            ->get();

        return view('teacher.task.index', compact(
            'title',
            'class',
            'course',
            'materials',
            'assignments'
        ));
    }
}
