@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4 mt-4">
        <div class="row align-items-center">
            <div class="col-md-4">
                <a href="{{ route('admin.classes.index') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>
                    Kembali
                </a>
            </div>

            {{-- TITLE --}}
            <div class="col-md-4 text-center mt-4">
                <h5 class="mb-1 fw-bold">Kelas Mata Pelajaran</h5>
                <small class="text-muted">
                    Kelola mata pelajaran dalam kelas pembelajaran
                </small>
            </div>

            <div class="col-md-4 text-end">
                <button type="button"
                        class="btn btn-dark btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addCourseModal">
                    <i class="bx bx-plus me-1"></i>
                    Tambah Mapel
                </button>
            </div>
        </div>
    </div>

    {{-- LIST MAPEL --}}
    <div class="row g-4 mt-3">
        @forelse($courses as $mapel)
        <div class="col-lg-6">
            <div class="card h-100 border shadow-sm">
                <div class="card-body">
                    <div class="d-flex">

                        {{-- COVER --}}
                        <div class="me-3 flex-shrink-0">
                            <div class="mapel-cover">
                                @php
                                    $gambarPath = $mapel->gambar
                                        ? public_path($mapel->gambar)
                                        : null;
                                @endphp

                                <img src="{{ ($mapel->gambar && file_exists($gambarPath))
                                        ? asset($mapel->gambar)
                                        : asset('assets/img/avatars/default-avatar.png') }}"
                                     alt="Cover Mapel"
                                     width="90"
                                     height="90"
                                     class="rounded">
                            </div>
                        </div>

                        {{-- CONTENT --}}
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-bold">{{ $mapel->name }}</h5>

                            <p class="text-muted mb-2">
                                {{ $mapel->description ?? 'Belum ada deskripsi mapel.' }}
                            </p>

                            <p class="text-muted mb-3">
                                <strong>Guru Pengampu:</strong><br>
                                {{ $mapel->teacher->user->name ?? 'Belum ditentukan' }}
                            </p>

                            <div class="d-flex gap-2">
                                {{-- HAPUS --}}
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCourseModal{{ $mapel->id }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                                
                                {{-- EDIT --}}
                                <button class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCourseModal{{ $mapel->id }}">
                                    <i class="bx bx-edit"></i>
                                </button>

                                {{-- DETAIL --}}
                                <a href="{{ route('admin.courses.tasks.index', [$schoolClass->id, $mapel->id]) }}"
                                class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 mt-4">
            <i class="bx bx-book-open text-muted" style="font-size:48px"></i>
            <p class="text-muted mt-2">Belum ada mata pelajaran.</p>
        </div>
        @endforelse
        @foreach($courses as $mapel)
        <div class="modal fade" id="editCourseModal{{ $mapel->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('admin.classes.courses.update', [$schoolClass->id, $mapel->id]) }}"
                        method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bx bx-edit"></i> Edit Mapel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label">Nama Mapel</label>
                                <input type="text"
                                    class="form-control"
                                    name="name"
                                    value="{{ $mapel->name }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Guru Pengampu</label>
                                <select class="form-select" name="teacher_id">
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            @selected($mapel->teacher_id == $teacher->id)>
                                            {{ $teacher->user->name }} ({{ $teacher->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control"
                                        name="description"
                                        rows="3">{{ $mapel->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cover (opsional)</label>
                                <input type="file" class="form-control" name="gambar">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-save"></i> Update
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @endforeach
        @foreach($courses as $mapel)
        <div class="modal fade" id="deleteCourseModal{{ $mapel->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('admin.classes.courses.destroy', [$schoolClass->id, $mapel->id]) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="modal-header">
                            <h5 class="modal-title text-danger">
                                <i class="bx bx-trash"></i> Hapus Mapel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-center">
                            <p>Yakin ingin menghapus mapel:</p>
                            <strong>{{ $mapel->name }}</strong>?
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn btn-danger">
                                <i class="bx bx-trash"></i> Hapus
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- MODAL TAMBAH MAPEL --}}
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.classes.courses.store', $schoolClass->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-book me-1"></i>
                        Tambah Mata Pelajaran
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="school_class_id" value="{{ $schoolClass->id }}">

                    <div class="mb-3">
                        <label class="form-label">Guru Pengampu</label>
                        <select class="form-select" name="teacher_id" required>
                            <option disabled selected>-- Pilih Guru --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->user->name }} ({{ $teacher->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Mapel</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               oninput="this.nextElementSibling.value =
                                        this.value.toLowerCase()
                                        .replace(/[^a-z0-9]+/g,'-')
                                        .replace(/(^-|-$)/g,'')"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" readonly>
                        <small class="text-muted">Slug dibuat otomatis</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Mapel</label>
                        <textarea class="form-control"
                                  name="description"
                                  rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cover Mapel (opsional)</label>
                        <input type="file"
                               class="form-control"
                               name="avatars"
                               accept="image/*">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
