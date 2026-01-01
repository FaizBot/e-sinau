<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CourseFormRequest;

class CourseController extends Controller
{
    public function index(SchoolClass $class)
    {
        $title = 'Mapel Management';

        $courses = Course::whereHas('schoolClasses', function ($q) use ($class) {
            $q->where('school_class_id', $class->id);
        })
        ->with('teacher.user')
        ->get();

        $teachers = Teacher::all();

        return view('admin.course', [
            'title' => $title,
            'courses' => $courses,
            'teachers' => $teachers,
            'schoolClass' => $class, // ⬅️ SATU kelas
        ]);
    }

    public function store(CourseFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $data['slug'] = Str::slug($data['name']) . '-' . uniqid();

            if ($request->hasFile('avatars')) {
                $file = $request->file('avatars');

                $filename = 'course-' .
                    Str::slug($data['name']) . '-' .
                    time() . '.' .
                    $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'assets/img/avatars',
                    $filename,
                    'public'
                );

                $data['avatars'] = 'storage/' . $path;
            }

            $course = Course::create($data);

            $selectedClasses = $request->input('school_classes');

            if (!$selectedClasses && $request->filled('school_class_id')) {
                $selectedClasses = [$request->school_class_id];
            }

            if ($selectedClasses) {
                $course->schoolClasses()->attach($selectedClasses);
            }

            DB::commit();

            $redirectClassId = is_array($selectedClasses) ? $selectedClasses[0] : null;

            return redirect()
                ->route('admin.classes.courses.index', $redirectClassId)
                ->with('success', 'Course berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->with('error', 'Gagal menambahkan course.')
                ->withInput();
        }
    }

    public function show(Course $course)
    {
        $course->load('teacher', 'assignments', 'schoolClasses');
        return view('courses.show', compact('course'));
    }

    public function update(CourseFormRequest $request, SchoolClass $class, Course $course)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($data['name']) . '-' . uniqid();

            if ($request->hasFile('avatars')) {

                // hapus foto lama
                if ($course->avatars) {
                    Storage::disk('public')->delete(
                        str_replace('storage/', '', $course->avatars)
                    );
                }

                $file = $request->file('avatars');
                $filename = 'course-' .
                    Str::slug($data['name']) . '-' .
                    time() . '.' .
                    $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'assets/img/avatars',
                    $filename,
                    'public'
                );

                $data['avatars'] = 'storage/' . $path;
            }

            $course->update($data);

            DB::commit();

            return redirect()
                ->route('admin.classes.courses.index', $class->id)
                ->with('success', 'Course berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->with('error', 'Gagal memperbarui course.')
                ->withInput();
        }
    }

    public function destroy(SchoolClass $class, Course $course)
    {
        DB::beginTransaction();

        try {
            if ($course->avatars) {
                Storage::disk('public')->delete(
                    str_replace('storage/', '', $course->avatars)
                );
            }

            $course->schoolClasses()->detach();

            $course->delete();

            DB::commit();

            return redirect()
                ->route('admin.classes.courses.index', $class->id)
                ->with('success', 'Course berhasil dihapus.');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Gagal menghapus course.');
        }
    }
}
