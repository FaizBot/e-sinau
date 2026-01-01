@extends('layout-admin')

    @section('content')
    <div class="container-fluid py-4">
        {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-1">Kelas</h6>
                    <p class="text-sm text-muted mb-0">
                        Manajemen kelas pembelajaran E-Sinau
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <span class="iconify me-1" data-icon="mdi:plus"></span>
                        Tambah Kelas
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
        <div class="row">

            @forelse ($classes as $kelas)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100 border shadow-sm">

                    <!-- COVER -->
                    <div class="position-relative">
                        <img
                            src="{{ asset($kelas->avatar ?? 'assets/img/avatars/default-avatar.png') }}"
                            class="img-fluid rounded-top w-100"
                            style="height:100px; object-fit:cover;"
                            alt="Cover Kelas">

                        <!-- ACTION -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                            <a href="javascript:void(0);"
                            class="btn btn-sm btn-icon btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#editClassModal{{ $kelas->id }}"
                            title="Edit">
                                <i class="bx bx-edit-alt"></i>
                            </a>

                            <form action="{{ route('admin.classes.destroy', $kelas->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                    <i class="bx bx-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editClassModal{{ $kelas->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.classes.update', $kelas->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Kelas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">Nama Kelas</label>
                                                <input type="text" class="form-control"
                                                    name="name" value="{{ $kelas->name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Kode Kelas</label>
                                                <input type="text" class="form-control"
                                                    name="code" value="{{ $kelas->code }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Wali Kelas</label>
                                                <select class="form-select" name="teacher_id">
                                                    <option value="">-- Pilih Wali Kelas --</option>
                                                    @foreach ($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}"
                                                            @selected($kelas->teacher_id == $teacher->id)>
                                                            {{ $teacher->user->name }} ({{ $teacher->nip }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <input type="text" class="form-control"
                                                    name="description"
                                                    value="{{ $kelas->description }}">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Cover Kelas</label>
                                                <input type="file" class="form-control" name="avatar">

                                                @if ($kelas->avatar)
                                                    <img src="{{ asset($kelas->avatar) }}"
                                                        class="img-fluid rounded mt-2"
                                                        style="height:80px; object-fit:cover;">
                                                @endif
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                            <button type="submit"
                                                    class="btn btn-primary">Update</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">{{ $kelas->name }}</h5>

                        <p class="text-muted mb-2">
                            {{ $kelas->description ?? 'Belum ada deskripsi kelas' }}
                        </p>

                        <p class="text-muted mb-1">
                            <strong>Wali Kelas:</strong><br>
                            {{ $kelas->teacher->user->name ?? 'Belum ditentukan' }}
                        </p>

                        <p class="text-muted mb-1">
                            <strong>Kode Kelas:</strong> {{ $kelas->code }}
                        </p>

                        <p class="text-muted mb-3">
                            <strong>Peserta:</strong> {{ $kelas->students_count }} siswa
                        </p>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.classes.plot', $kelas->id) }}"
                               class="btn btn-outline-danger">
                                <span class="iconify me-1" data-icon="mdi:account-multiple-plus-outline"></span>
                                Kelola Peserta
                            </a>

                            <a href="{{ route('admin.classes.courses.index', $kelas->id) }}"
                               class="btn btn-outline-primary">
                                <span class="iconify me-1" data-icon="mdi:eye-outline"></span>
                                Detail Kelas
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <span class="iconify mb-2 text-muted" data-icon="mdi:google-classroom" style="font-size:48px"></span>
                <p class="text-muted">Belum ada kelas pembelajaran.</p>
            </div>
            @endforelse

        </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Kelas</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Wali Kelas</label>
                        <select class="form-select" name="teacher_id">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->user->name }} ({{ $teacher->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" name="description">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cover Kelas</label>
                        <input type="file" class="form-control" name="avatar" accept="image/*">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
