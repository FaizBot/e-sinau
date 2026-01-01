<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function indexadmin()
    {
        return view('admin.dashboard', [
            'totalStudents' => Student::count(),
            'totalTeachers' => Teacher::count(),
            'totalClasses'  => SchoolClass::count(),
            'totalTasks'    => Assignment::count(),

            'latestTasks' => Assignment::with('schoolClass')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function indexteacher()
    {
        $teacher = Auth::user()->teacher;

        $classIds = $teacher->schoolClasses()->pluck('id');

        return view('teacher.dashboard', [
            'classCount' => $classIds->count(),
            'taskCount'  => Assignment::whereIn('school_class_id', $classIds)->count(),
            'pending'    => Result::where('status', 'pending')
                            ->whereIn('assignment_id', function ($q) use ($classIds) {
                                $q->select('id')
                                ->from('assignments')
                                ->whereIn('school_class_id', $classIds);
                            })
                            ->count(),

            'tasks'      => Assignment::whereIn('school_class_id', $classIds)
                                ->latest()
                                ->limit(5)
                                ->get(),
        ]);
    }

    public function indexstudent()
    {
        $student = Auth::user()->student;

        $class = $student->schoolClass; // relasi siswa → kelas

        $courseCount = $class
            ? $class->courses()->count()
            : 0;

        return view('student.dashboard', [
            'courseCount' => $courseCount,
            'taskCount'   => Assignment::count(),
            'pending'     => Result::where('student_id', $student->id)
                                ->whereNull('total_score')
                                ->count(),

            'tasks' => Assignment::latest()->limit(5)->get(),
        ]);
    }
}