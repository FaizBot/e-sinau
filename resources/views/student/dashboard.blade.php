@extends('layout-student')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        Dashboard Admin
    </h4>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Mata Pelajaran</small>
                    <h3 class="mb-0">{{ $courseCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total Tugas Saya</small>
                    <h3 class="mb-0">{{ $taskCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Belum Dikerjakan</small>
                    <h3 class="mb-0">{{ $pending }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Tugas Terdekat</div>
        <ul class="list-group list-group-flush">
            @foreach ($tasks as $task)
            <li class="list-group-item">
                {{ $task->title }}
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection