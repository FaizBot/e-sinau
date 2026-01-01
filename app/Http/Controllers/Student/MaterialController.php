<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Course;
use App\Models\SchoolClass;

class MaterialController extends Controller
{
    public function preview(SchoolClass $class, Course $course, Material $material)
    {
        $title = 'Preview Materi';

        return view('student.task.materials.preview', compact(
            'title',
            'class',
            'course',
            'material'
        ));
    }
}
