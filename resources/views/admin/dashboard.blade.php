@extends('layout-admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Dashboard Admin
    </h4>
    <div class="row g-3 mb-4">
        @foreach ([
            'Siswa' => $totalStudents,
            'Guru' => $totalTeachers,
            'Kelas' => $totalClasses,
            'Tugas' => $totalTasks
        ] as $label => $value)
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $value }}</h3>
                        <small class="text-muted">{{ $label }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">Tugas Terbaru</div>
        <table class="table mb-0">
            @foreach ($latestTasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ $task->schoolClass->name }}</td>
                <td>
                    <span class="badge bg-secondary">
                        {{ ucfirst($task->corrected) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection