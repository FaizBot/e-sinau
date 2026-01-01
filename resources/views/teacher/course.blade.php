@extends('layout-teacher')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4 mt-4">
        <div class="row align-items-center">
            <div class="col-md-4">
                <a href="{{ route('teacher.classes.index') }}"
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
                                {{-- DETAIL --}}
                                <a href="{{ route('teacher.courses.tasks.index', [$mapel->schoolClasses->first()->id, $mapel->id]) }}"
                                class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show"></i> Detail
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
    </div>
</div>
@endsection
