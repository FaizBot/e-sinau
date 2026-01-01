<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SchoolClassController extends Controller
{
    public function index()
    {
        $title = 'Kelas';

        $classes = SchoolClass::with(['teacher.user'])
            ->withCount('students')
            ->get();

        $teachers = Teacher::with('user')
            ->where('type', 'teacher')
            ->get()
            ->sortBy(fn ($t) => $t->user->name);

        return view('admin.class', compact('classes', 'title', 'teachers'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'code' => 'required|string|unique:school_classes,code',
                'teacher_id' => 'nullable|exists:teachers,id',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $avatarPath = null;

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                $filename = 'avatars-kelas-' .
                    Str::slug($request->name) . '-' .
                    time() . '.' .
                    $file->getClientOriginalExtension();

                $file->move(public_path('assets/img/avatars'), $filename);

                $avatarPath = 'assets/img/avatars/' . $filename;
            }

            $schoolClass = SchoolClass::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'slug'        => Str::slug($validated['name']).'-'.uniqid(),
                'code'        => $validated['code'],
                'teacher_id'  => $validated['teacher_id'] ?? null,
                'avatar'      => $avatarPath,
            ]);

            if (!empty($validated['teacher_id'])) {
                Teacher::where('id', $validated['teacher_id'])
                    ->update(['type' => 'classroomTeacher']);
            }

            DB::commit();

            return redirect()
                ->route('admin.classes.index')
                ->with('success', 'Kelas berhasil dibuat.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show(SchoolClass $schoolClass)
    {
        return view('school_classes.show', compact('schoolClass'));
    }

    public function edit(SchoolClass $schoolClass)
    {
        $teachers = Teacher::all();
        return view('school_classes.edit', compact('schoolClass', 'teachers'));
    }

    public function update(Request $request, SchoolClass $schoolClass)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'code'        => 'required|string|unique:school_classes,code,' . $schoolClass->id,
                'teacher_id'  => 'nullable|exists:teachers,id',
                'description' => 'nullable|string',
                'avatar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $avatarPath = $schoolClass->avatar;

            if ($request->hasFile('avatar')) {

                if ($avatarPath && File::exists(public_path($avatarPath))) {
                    File::delete(public_path($avatarPath));
                }

                $file = $request->file('avatar');
                $filename = 'avatars-kelas-' .
                    Str::slug($validated['name']) . '-' .
                    time() . '.' .
                    $file->getClientOriginalExtension();

                $file->move(public_path('assets/img/avatars'), $filename);

                $avatarPath = 'assets/img/avatars/' . $filename;
            }

            $oldTeacherId = $schoolClass->teacher_id;
            $newTeacherId = $validated['teacher_id'] ?? null;

            if ($oldTeacherId && $oldTeacherId != $newTeacherId) {
                Teacher::where('id', $oldTeacherId)
                    ->update(['type' => 'teacher']);
            }

            if ($newTeacherId) {
                Teacher::where('id', $newTeacherId)
                    ->update(['type' => 'classroomTeacher']);
            }

            $schoolClass->update([
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['name']) . '-' . $schoolClass->id,
                'code'        => $validated['code'],
                'teacher_id'  => $newTeacherId,
                'description' => $validated['description'] ?? null,
                'avatar'      => $avatarPath,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.classes.index')
                ->with('success', 'Kelas berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(SchoolClass $schoolClass)
    {
        DB::transaction(function () use ($schoolClass) {

            $teacherId = $schoolClass->teacher_id;
            $avatarPath = $schoolClass->avatar;

            if ($avatarPath && File::exists(public_path($avatarPath))) {
                File::delete(public_path('assets/img/avatars'), $avatarPath);
            }

            $schoolClass->delete();

            if ($teacherId) {
                Teacher::where('id', $teacherId)
                    ->update(['type' => 'teacher']);
            }
        });

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Kelas dan cover berhasil dihapus.');
    }

    public function showPlotingForm(SchoolClass $schoolClass)
    {
        $title = 'Plot Siswa ke Kelas';

        $studentsNotPloted = Student::whereDoesntHave('schoolClasses', function ($query) use ($schoolClass) {
                $query->where('school_class_id', $schoolClass->id);
            })
            ->with('user')
            ->orderBy('nis')
            ->get();

        $schoolClass->load(['students.user']);

        return view('admin.ploting', compact(
            'schoolClass',
            'studentsNotPloted',
            'title'
        ));
    }

    public function storePloting(Request $request, SchoolClass $schoolClass)
    {
        $validated = $request->validate([
            'student_ids'   => ['required', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        $schoolClass->students()
            ->syncWithoutDetaching($validated['student_ids']);

        return redirect()
            ->route('admin.classes.plot', $schoolClass->id)
            ->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function removePloting(SchoolClass $schoolClass, Student $student)
    {
        $schoolClass->students()->detach($student->id);

        return redirect()
            ->route('admin.classes.plot', $schoolClass->id)
            ->with('success', 'Siswa berhasil dihapus dari kelas.');
    }
}

