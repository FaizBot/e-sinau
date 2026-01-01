<?php

namespace App\Http\Controllers\Student;

use App\Models\Course;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(SchoolClass $class, Course $course)
    {
        $title = 'Daftar Tugas dan Materi';

        $student = Auth::user()->student;

        $materials = $course->materials()
            ->where('is_published', 1)
            ->orderBy('order_number')
            ->latest()
            ->get();

        $assignments = $course->assignments()
            ->where('school_class_id', $class->id)
            ->where('is_published', 1)
            ->orderBy('order_number')
            ->latest()
            ->withCount('questions')
            ->with([
                'results' => function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                }
            ])
            ->get();

        return view('student.task.index', compact(
            'title',
            'class',
            'course',
            'materials',
            'assignments'
        ));
    }
}
