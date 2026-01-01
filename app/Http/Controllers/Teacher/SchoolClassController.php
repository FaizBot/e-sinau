<?php

namespace App\Http\Controllers\Teacher;

use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class SchoolClassController extends Controller
{
    public function index()
    {
        $title = 'Manajemen Kelas Saya';
        $user = Auth::user();

        // pastikan teacher & wali kelas
        if (
            $user->role !== 'teacher' ||
            !$user->teacher ||
            $user->teacher->type !== 'classroomTeacher'
        ) {
            abort(403);
        }

        // ambil class berdasarkan teacher_id (auth)
        $classes = SchoolClass::where('teacher_id', $user->teacher->id)->get();

        return view('teacher.class', compact('classes', 'title'));
    }
}