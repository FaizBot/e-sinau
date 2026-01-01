<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // public function index(SchoolClass $class, Course $course)
    // {
    //     $title = 'Material Management';

    //     $materials = $course->materials()
    //         ->orderBy('order_number')
    //         ->get();

    //     return view(
    //         'admin.materials.index',
    //         compact('title', 'class', 'course', 'materials')
    //     );
    // }

    public function create(SchoolClass $class, Course $course)
    {
        $title = 'Tambah Materi';
        return view('admin.task.materials.create', compact('class', 'course', 'title'));
    }

    public function store(Request $request, SchoolClass $class, Course $course)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'nullable|string',
            'file'        => 'nullable|file|max:10240',
            'video_url'   => 'nullable|url',
            'type'        => 'required|in:text,file,video',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'course_id'   => $course->id,
                'teacher_id'  => $course->teacher_id,
                'title'       => $request->title,
                'slug'        => Str::slug($request->title) . '-' . uniqid(),
                'description' => $request->description,
                'type'        => $request->type,
                'is_published'=> false,
                'order_number'=> ($course->materials()->max('order_number') ?? 0) + 1,
            ];

            $data['content']   = null;
            $data['file_path'] = null;
            $data['video_url'] = null;

            if ($request->type === 'text') {
                $data['content'] = $request->content;
            }

            if ($request->type === 'file' && $request->hasFile('file')) {
                $path = $request->file('file')->store('materials/files', 'public');
                $data['file_path'] = 'storage/' . $path;
            }

            if ($request->type === 'video') {
                $data['video_url'] = $request->video_url;
            }

            Material::create($data);
            
            DB::commit();

            return redirect()
                ->route('admin.courses.tasks.index', [$class->id, $course->id])
                ->with('success', 'Materi berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->with('error', 'Gagal menambahkan materi.')
                ->withInput();
        }
    }

    public function edit(SchoolClass $class, Course $course, Material $material)
    {
        $title = 'Edit Materi';

        return view('admin.task.materials.edit', compact(
            'title',
            'class',
            'course',
            'material'
        ));
    }

    public function update(Request $request, $classId, $courseId, $materialId)
    {
        $material = Material::findOrFail($materialId);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:text,file,video',
            'content'     => 'nullable|string',
            'description' => 'nullable|string',
            'video_url'   => 'nullable|url',
            'file'        => 'nullable|file|max:10240',
        ]);

        // ================= TEXT =================
        if ($data['type'] === 'text') {
            $data['file_path']   = null;
            $data['video_url']   = null;
            $data['description'] = null;
        }

        // ================= VIDEO =================
        if ($data['type'] === 'video') {
            $data['file_path'] = null;
            $data['content']   = null;
        }

        // ================= FILE =================
        if ($data['type'] === 'file') {

            $data['content']   = null;
            $data['video_url'] = null;

            // ✅ JIKA ADA FILE BARU
            if ($request->hasFile('file')) {

                // hapus file lama (optional tapi rapi)
                if ($material->file_path && file_exists(public_path($material->file_path))) {
                    unlink(public_path($material->file_path));
                }

                $file = $request->file('file');
                $filename = time().'_'.$file->getClientOriginalName();
                $path = $file->storeAs('materials', $filename, 'public');

                $data['file_path'] = 'storage/'.$path;
            }
            // ❗ JIKA TIDAK UPLOAD FILE
            else {
                $data['file_path'] = $material->file_path; // 🔥 PERTAHANKAN FILE LAMA
            }
        }

        $material->update($data);

        return redirect()
            ->route('admin.courses.tasks.index', [$classId, $courseId])
            ->with('success', 'Materi berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $material = Material::findOrFail($id);

            // soft delete
            $material->delete();

            DB::commit();

            return back()->with('success', 'Materi berhasil dihapus.');

        } catch (\Throwable $e) {

            DB::rollBack();
            report($e);

            return back()->with('error', 'Gagal menghapus materi.');
        }
    }

    public function togglePublish(SchoolClass $class, Course $course, Material $material)
    {
        $material->update([
            'is_published' => !$material->is_published
        ]);

        return back()->with(
            'success',
            $material->is_published
                ? 'Materi berhasil dipublikasikan.'
                : 'Materi berhasil disembunyikan.'
        );
    }

    public function preview(SchoolClass $class, Course $course, Material $material)
    {
        $title = 'Preview Materi';

        return view('admin.task.materials.preview', compact(
            'title',
            'class',
            'course',
            'material'
        ));
    }
}
