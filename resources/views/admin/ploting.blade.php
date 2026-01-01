@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1 fw-bold">
                    Plot Siswa ke Kelas
                </h5>
                <small class="text-muted">
                    Kelas: <strong>{{ $schoolClass->name }}</strong>
                </small>
            </div>

            <div class="col-md-4 text-end">
                <a href="{{ route('admin.classes.index') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- FORM PLOT SISWA --}}
        <div class="col-lg-6">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold">
                        Tambahkan Siswa ke Kelas
                    </h6>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.classes.plot.store', $schoolClass->id) }}"
                          method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Siswa Belum Masuk Kelas
                            </label>

                            <select name="student_ids[]"
                                    class="form-select"
                                    multiple
                                    required
                                    style="min-height: 160px">
                                @forelse ($studentsNotPloted as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->user->name }} — {{ $student->nis }}
                                    </option>
                                @empty
                                    <option disabled>
                                        Tidak ada siswa tersedia
                                    </option>
                                @endforelse
                            </select>

                            <small class="text-muted">
                                Gunakan <strong>Ctrl / Shift</strong> untuk memilih lebih dari satu
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Tambahkan ke Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- DAFTAR SISWA DALAM KELAS --}}
        <div class="col-lg-6">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold">
                        Daftar Siswa di Kelas Ini
                    </h6>
                </div>

                <div class="card-body p-0">
                    @if ($schoolClass->students->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-user-x mb-2" style="font-size:32px"></i>
                            <p class="mb-0">Belum ada siswa di kelas ini</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($schoolClass->students as $student)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $student->user->name }}</strong>
                                        <div class="text-muted small">
                                            NIS: {{ $student->nis }}
                                        </div>
                                    </div>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteStudentModal{{ $student->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </li>
                                {{-- MODAL HAPUS SISWA --}}
                                <div class="modal fade"
                                    id="deleteStudentModal{{ $student->id }}"
                                    tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h6 class="modal-title">
                                                    <i class="bx bx-trash text-danger me-1"></i>
                                                    Hapus Siswa
                                                </h6>
                                                <button type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body text-center">
                                                <p class="mb-1">
                                                    Yakin ingin menghapus siswa:
                                                </p>
                                                <strong>{{ $student->user->name }}</strong>
                                                <div class="text-muted small">
                                                    NIS: {{ $student->nis }}
                                                </div>
                                            </div>

                                            <div class="modal-footer justify-content-center">
                                                <button type="button"
                                                        class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">
                                                    Batal
                                                </button>

                                                <form method="POST"
                                                    action="{{ route('admin.classes.plot.remove', [$schoolClass->id, $student->id]) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-danger btn-sm">
                                                        <i class="bx bx-trash me-1"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
