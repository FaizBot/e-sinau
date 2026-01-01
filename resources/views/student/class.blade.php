@extends('layout-student')

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
                        Kelas pembelajaran E-Sinau
                    </p>
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
                            <img src="{{ asset($kelas->avatar ?? 'assets/img/avatars/default-avatar.png') }}"
                                class="img-fluid rounded-top w-100" style="height:100px; object-fit:cover;"
                                alt="Cover Kelas">
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
                                <a href="{{ route('student.classes.courses.index', $kelas->id) }}"
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
                    <span class="iconify mb-2 text-muted" data-icon="mdi:google-classroom"
                        style="font-size:48px"></span>
                    <p class="text-muted">Belum ada kelas pembelajaran.</p>
                </div>
                @endforelse

            </div>
        </div>
    </div>
</div>
@endsection