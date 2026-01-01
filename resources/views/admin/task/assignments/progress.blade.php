@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="mb-1">{{ $assignment->title }}</h5>
                <small class="text-muted">
                    Progres Siswa • Kelas {{ $assignment->schoolClass->name }}
                </small>
            </div>

            <a href="{{ route('admin.courses.tasks.index', [$class->id, $course->id]) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table id="progressTable" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->nis }}</td>
                            <td>{{ $student->name }}</td>

                            {{-- STATUS --}}
                            <td>
                                @if ($student->status === 'completed')
                                    <span class="badge bg-success-subtle text-success">
                                        Selesai
                                    </span>
                                @elseif ($student->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning">
                                        Perlu Dinilai
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Belum Mengerjakan
                                    </span>
                                @endif
                            </td>

                            {{-- NILAI --}}
                            <td>
                                {{ $student->score !== null ? $student->score : '-' }}
                            </td>

                            {{-- AKSI --}}
                            <td class="d-flex gap-1 flex-wrap">

                                @if ($student->status === null)
                                    <span class="text-muted">–</span>

                                @elseif ($assignment->corrected === 'system')
                                    <a href="{{ route('admin.assignment.review', [
                                        'class' => $class->id,
                                        'course' => $course->id,
                                        'assignment' => $assignment->id,
                                        'nis' => $student->nis,
                                    ]) }}"
                                    class="btn btn-sm btn-outline-info">
                                        Lihat Jawaban
                                    </a>

                                @elseif ($assignment->corrected === 'teacher')

                                    @if ($student->score === null)
                                        <a href="{{ route('admin.assignment.grade', [
                                            'class' => $class->id,
                                            'course' => $course->id,
                                            'assignment' => $assignment->id,
                                            'nis' => $student->nis,
                                        ]) }}"
                                        class="btn btn-sm btn-primary">
                                            Nilai
                                        </a>
                                    @else
                                        <a href="{{ route('admin.assignment.review', [
                                            'class' => $class->id,
                                            'course' => $course->id,
                                            'assignment' => $assignment->id,
                                            'nis' => $student->nis,
                                        ]) }}"
                                        class="btn btn-sm btn-outline-info">
                                            Lihat
                                        </a>

                                        <a href="{{ route('admin.assignment.grade', [
                                            'class' => $class->id,
                                            'course' => $course->id,
                                            'assignment' => $assignment->id,
                                            'nis' => $student->nis,
                                        ]) }}"
                                        class="btn btn-sm btn-outline-warning">
                                            Ubah Nilai
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@push('scripts')
<script>
    $(document).ready(function () {
        $('#progressTable').DataTable({
            pageLength: 10,
            ordering: false,
            responsive: true,
            language: {
                search: "Cari siswa:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
</script>
@endpush
@endsection
