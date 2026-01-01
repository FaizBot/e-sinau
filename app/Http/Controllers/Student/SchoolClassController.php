<?php

namespace App\Http\Controllers\Student;

use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SchoolClassController extends Controller
{
    public function index()
    {
        $title = 'Kelas Saya';

        $studentId = Auth::user()->student->id;

        $classes = SchoolClass::whereHas('students', function ($query) use ($studentId) {
            $query->where('student_id', $studentId);
        })->with('teacher', 'students')->get();

        $teachers = Teacher::with('user')->get();

        return view('student.class', compact('classes', 'title', 'teachers'));
    }

    public function showJoinForm()
    {
        $title = 'Bergabung Kelas';
        return view('student.join-class')->with('title', $title);
    }

    public function joinClass(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $class = SchoolClass::where('code', $request->code)->first();

        if (!$class) {
            return back()->withErrors(['code' => 'Kode kelas tidak ditemukan.']);
        }

        $student = Auth::user()->student;

        if ($class->students()->where('student_id', $student->id)->exists()) {
            return back()->with('status', 'Anda sudah tergabung dalam kelas ini.');
        }

        $class->students()->attach($student->id);

        return redirect()->route('student.classes.join.form')->with('status', 'Berhasil bergabung ke kelas!');
    }
}
