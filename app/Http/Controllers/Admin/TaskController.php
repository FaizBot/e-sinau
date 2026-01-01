<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(SchoolClass $class, Course $course)
    {
        $teacher = Auth::user()->teacher;

        // wajib login sebagai teacher
        abort_if(!$teacher, 403);

        // (opsional) pastikan course memang ada di kelas tsb
        abort_if(
            !$course->schoolClasses()
                ->where('school_classes.id', $class->id)
                ->exists(),
            404
        );

        $title = 'Daftar Tugas dan Materi';

        $materials = $course->materials()
            ->orderBy('order_number')
            ->latest()
            ->get();

        $assignments = $course->assignments()
            ->where('school_class_id', $class->id)
            ->orderBy('order_number')
            ->latest()
            ->withCount('questions')
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
