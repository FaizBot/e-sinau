<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Course;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(SchoolClass $class)
    {
        $teacher = Auth::user()->teacher;

        // HARUS wali kelas
        abort_if(
            !$teacher ||
            $teacher->type !== 'classroomTeacher' ||
            $class->teacher_id !== $teacher->id,
            403
        );

        $title = 'Mapel Kelas';

        // Ambil SEMUA mapel dalam kelas tsb
        $courses = Course::whereHas('schoolClasses', function ($q) use ($class) {
                $q->where('school_classes.id', $class->id);
            })
            ->with('teacher.user') // guru mapel
            ->get();

        return view('teacher.course', [
            'title'       => $title,
            'courses'     => $courses,
            'schoolClass' => $class,
        ]);
    }

    

    public function teacher()
    {
        $teacher = Auth::user()->teacher;
        // abort_if(!$teacher, 403);

        $title = 'Mapel';

        $courses = Course::whereHas('schoolClasses', function ($q) use ($teacher) {
            $q->where('school_classes.teacher_id', $teacher->id);
        })
        ->when($teacher->type === 'teacher', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
        ->with(['schoolClasses', 'assignments'])
        ->get();


        return view('teacher.course', [
            'title'       => $title,
            'courses'     => $courses,
        ]);
    }
}
