@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $assignment->title }}</h5>
                <small class="text-muted">
                    {{ $course->name }} • Kelas {{ $class->name }} • {{ $assignment->created_at->format('d M Y H:i') }}
                </small>
            </div>

            <span class="badge {{ $assignment->is_published ? 'bg-success' : 'bg-secondary' }}">
                {{ $assignment->is_published ? 'Publish' : 'Draft' }}
            </span>
        </div>
    </div>

    {{-- DAFTAR SOAL --}}
    @foreach ($assignment->questions as $index => $question)
        <div class="card mb-3">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <strong>Soal {{ $index + 1 }}</strong>
                    <span class="badge bg-label-primary">
                        {{ $question->point }} poin
                    </span>
                </div>

                <p class="mb-3">
                    {{ $question->question_text }}
                </p>

                {{-- PILIHAN GANDA --}}
                @if ($question->question_type === 'multiple_choice' && $question->option)
                    @php
                        $rawOptions = $question->option->option_text;

                        // JIKA STRING → decode
                        if (is_string($rawOptions)) {
                            $options = json_decode($rawOptions, true) ?? [];
                        } else {
                            $options = $rawOptions;
                        }

                        $correct = $question->option->correct_option;
                    @endphp

                    <ul class="list-group">
                        @foreach ($options as $i => $opt)
                            <li class="list-group-item
                                {{ $isTeacher && ($opt['text'] ?? '') === $correct ? 'list-group-item-success' : '' }}">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        {{ chr(65 + $i) }}. {{ $opt['text'] ?? '-' }}
                                    </span>

                                    @if ($isTeacher && ($opt['text'] ?? '') === $correct)
                                        <span class="badge bg-success">
                                            Jawaban Benar
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- ESSAY --}}
                @if ($question->question_type === 'essay')
                    <div class="alert alert-secondary">
                        <em>Soal Essay</em>

                        @if ($isTeacher)
                            <div class="mt-2 text-success">
                                <i class="bx bx-info-circle"></i>
                                Dinilai manual oleh guru
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    @endforeach

    <div class="text-end mt-4">
        <a href="{{ route('admin.courses.tasks.index', [$class->id, $course->id]) }}"
           class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>

</div>
@endsection
