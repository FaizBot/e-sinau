@extends('layout-teacher')

@section('content')

<style>
    body {
        background-color: #f5f5f9;
    }

    /* HEADER SLIM */
    .task-header {
        background: #fff;
        border-radius: 8px;
        padding: 16px 20px;
        border: 1px solid #e7e7ef;
    }

    .task-header h5 {
        margin-bottom: 4px;
        font-weight: 600;
        color: #566a7f;
    }

    .task-header small {
        color: #8592a3;
    }

    /* TAB SNEAT */
    .nav-tabs {
        border-bottom: 1px solid #e7e7ef;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #8592a3;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #696cff;
        border-bottom: 2px solid #696cff;
        background: transparent;
    }

    /* CARD */
    .task-card {
        border: 1px solid #e7e7ef;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(67, 89, 113, 0.08);
    }

    .task-card h6 {
        font-weight: 600;
        color: #566a7f;
    }

    .task-card p {
        color: #8592a3;
    }

    .badge-soft {
        background-color: #e7e7ff;
        color: #696cff;
        font-weight: 500;
    }

    .task-card-soft {
        border-radius: 14px;
        transition: all 0.2s ease;
    }

    .task-card-soft:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,.08);
    }

    .bg-label-success {
        background-color: rgba(40, 199, 111, 0.12);
        color: #28c76f;
    }

    .bg-label-warning {
        background-color: rgba(255, 159, 67, 0.15);
        color: #ff9f43;
    }

</style>

<div class="container-fluid py-4">

    {{-- ================= HEADER ================= --}}
    <div class="task-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>
                <h5>{{ $course->name }}</h5>
                <small>
                    {{ $course->name }} • Kelas {{ $class->name }}
                </small>
            </div>

            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('teacher.classes.courses.index', $class->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back"></i>
                    Kembali
                </a>

                <a href="{{ route('teacher.courses.materials.create', [$class->id, $course->id]) }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-book-add"></i>
                    Tambah Materi
                </a>

                <a href="{{ route('teacher.assignment.create', [$class->id, $course->id]) }}" class="btn btn-warning btn-sm text-dark">
                    <i class="bx bx-edit-alt"></i>
                    Tambah Soal
                </a>
            </div>

        </div>
    </div>

    {{-- ================= TAB ================= --}}
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#materi">
                Materi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#soal">
                Soal
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane fade show active" id="materi">
            <div class="row g-3">

                @forelse ($materials as $material)
                    <div class="col-md-6">
                        <div class="card task-card">
                            <div class="card-body">

                                <h6>{{ $material->title }}</h6>

                                <p class="small mb-3">
                                    {{ Str::limit($material->description, 80) ?? 'Tidak ada deskripsi' }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">

                                    {{-- TYPE --}}
                                    <span class="badge badge-soft">
                                        {{ strtoupper($material->type) }}
                                    </span>

                                    @if ($material->is_published)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bx bx-globe"></i> Publish
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bx bx-lock"></i> Draft
                                        </span>
                                    @endif

                                    {{-- ACTION --}}
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('teacher.courses.materials.edit', [$class->id, $course->id, $material->id]) }}"
                                        class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-edit"></i>
                                        </a>

                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteMaterial{{ $material->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>

                                        <div class="modal fade" id="deleteMaterial{{ $material->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h6 class="modal-title text-danger">
                                                            Hapus Materi?
                                                        </h6>
                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body text-center">
                                                        <p class="small text-muted mb-2">
                                                            Materi:
                                                        </p>
                                                        <strong>{{ $material->title }}</strong>
                                                        <p class="small text-muted mt-2 mb-0">
                                                            Tindakan ini tidak dapat dibatalkan.
                                                        </p>
                                                    </div>

                                                    <div class="modal-footer justify-content-center">
                                                        <form
                                                            method="POST"
                                                            action="{{ route('teacher.courses.materials.destroy', [$class->id, $course->id, $material->id]) }}">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button class="btn btn-danger btn-sm">
                                                                Ya, Hapus
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                                            Batal
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#publishModal{{ $material->id }}"
                                        >
                                            <i class="bx bx-upload"></i>
                                        </button>

                                        <div class="modal fade" id="publishModal{{ $material->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h6 class="modal-title">
                                                            {{ $material->is_published ? 'Sembunyikan Materi?' : 'Publikasikan Materi?' }}
                                                        </h6>
                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body text-center">
                                                        <p class="small text-muted mb-0">
                                                            Materi:
                                                            <strong>{{ $material->title }}</strong>
                                                        </p>
                                                    </div>

                                                    <div class="modal-footer justify-content-center">
                                                        <form
                                                            method="POST"
                                                            action="{{ route('teacher.materials.publish', [$class->id, $course->id, $material->id]) }}"
                                                        >
                                                            @csrf
                                                            @method('PATCH')

                                                            <button class="btn btn-primary btn-sm">
                                                                {{ $material->is_published ? 'Unpublish' : 'Publish' }}
                                                            </button>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('teacher.courses.materials.preview', [$class->id, $course->id, $material->id]) }}"
                                        class="btn btn-sm btn-outline-info">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-book mb-2 fs-2"></i>
                            <p>Belum ada materi</p>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>

        <div class="tab-pane fade" id="soal">
            <div class="row g-3">

                @forelse ($assignments as $assignment)
                    <div class="col-md-6">
                        <div class="card task-card">
                            <div class="card-body">

                                {{-- TITLE --}}
                                <h6>{{ $assignment->title }}</h6>

                                {{-- DESCRIPTION --}}
                                <p class="small mb-1">
                                    {{ $assignment->description
                                        ? Str::limit($assignment->description, 80)
                                        : 'Tidak ada deskripsi' }}
                                </p>

                                {{-- CREATED AT --}}
                                <p class="small text-muted mb-2">
                                    Dibuat: {{ $assignment->created_at->format('d M Y H:i') }}
                                </p>

                                {{-- TOTAL QUESTIONS --}}
                                <p class="small text-muted mb-3">
                                    Jumlah Soal: {{ $assignment->questions_count ?? $assignment->questions->count() }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">

                                    {{-- TYPE --}}
                                    <span class="badge badge-soft">
                                        {{ strtoupper($assignment->corrected) }}
                                    </span>

                                    {{-- STATUS --}}
                                    @if ($assignment->is_published)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bx bx-globe"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bx bx-lock"></i> Draft
                                        </span>
                                    @endif

                                    {{-- ACTION --}}
                                    <div class="d-flex gap-1">
                                        {{-- EDIT, DELETE, PUBLISH, PREVIEW --}}
                                        <a href="{{ route('teacher.assignment.progress', [
                                            'class' => $class->id,
                                            'course' => $course->id,
                                            'assignment' => $assignment->id
                                        ]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-user-check"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAssignment{{ $assignment->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        <div class="modal fade" id="deleteAssignment{{ $assignment->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h6 class="modal-title text-danger"> Hapus Soal? </h6>
                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <strong>{{ $assignment->title }}</strong>
                                                        <p class="small text-muted mt-2 mb-0"> Tindakan ini tidak dapat dibatalkan. </p>
                                                    </div>
                                                    <div class="modal-footer justify-content-center">
                                                        <form method="POST" action="{{ route('teacher.assignment.destroy', [
                                                            'class' => $class->id,
                                                            'course' => $course->id,
                                                            'assignment' => $assignment->id
                                                        ]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger btn-sm"> Ya, Hapus </button>
                                                        </form>
                                                        <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"> Batal </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PUBLISH --}}
                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#publishAssignment{{ $assignment->id }}">
                                            <i class="bx bx-upload"></i>
                                        </button>

                                        <div class="modal fade" id="publishAssignment{{ $assignment->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h6 class="modal-title">
                                                            {{ $assignment->is_published ? 'Nonaktifkan Soal?' : 'Aktifkan Soal?' }}
                                                        </h6>
                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body text-center">
                                                        <p class="small text-muted mb-0">
                                                            Soal:
                                                            <strong>{{ $assignment->title }}</strong>
                                                        </p>
                                                    </div>

                                                    <div class="modal-footer justify-content-center">
                                                        <form method="POST"
                                                            action="{{ route('teacher.assignment.publish', [$class->id, $course->id, $assignment->id]) }}">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button class="btn btn-primary btn-sm">
                                                                {{ $assignment->is_published ? 'Nonaktifkan' : 'Aktifkan' }}
                                                            </button>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- PREVIEW --}}
                                        <a href="{{ route('teacher.assignment.show', [$class->id, $course->id, $assignment->id]) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bx bx-show"></i>
                                        </a>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-task mb-2 fs-2"></i>
                            <p>Belum ada soal</p>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</div>

@endsection
