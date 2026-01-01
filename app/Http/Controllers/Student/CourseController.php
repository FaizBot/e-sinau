<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index($id)
    {
        $title = 'Mapel Pelajaran';

        $courses = Course::whereHas('schoolClasses', function ($query) use ($id) {
            $query->where('school_class_id', $id);
        })->with('teacher')->get();

        $teachers = Teacher::all();

        $course = Course::findOrFail($id);
        $schoolClass = $course->schoolClasses->first();

        return view('student.course', compact('courses', 'teachers', 'schoolClass', 'id', 'title'));
    }
}