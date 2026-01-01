@extends('layout-teacher')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Dashboard Guru
    </h4>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Kelas</small>
                    <h3 class="mb-0">{{ $classCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Mapel</small>
                    <h3 class="mb-0">{{ $taskCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Perlu Dinilai</small>
                    <h3 class="mb-0">{{ $pending }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Tugas Terbaru</div>
        <table class="table mb-0">
            @foreach ($tasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>
                    <a href="#">Nilai</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection