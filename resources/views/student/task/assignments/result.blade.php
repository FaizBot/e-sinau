@extends('layout-student')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-3">
        <a href="{{ route('student.courses.tasks.index', [$class, $course]) }}"
        class="btn btn-outline-secondary btn-sm">
            ← Kembali ke Daftar Tugas
        </a>
    </div>

    {{-- STATUS --}}
    @if ($result->status === 'pending')
        <div class="alert alert-warning">
            Jawaban telah dikirim dan menunggu penilaian guru.
        </div>
    @else
        <div class="alert alert-success">
            Nilai Akhir: <strong>{{ $result->total_score }}</strong>
        </div>
    @endif

    @foreach ($questions as $index => $question)
    @php
        $studentAnswer = $studentAnswers[$question->id] ?? null;
        $earned = $points[$question->id] ?? 0;

        $options = is_string(optional($question->option)->option_text)
            ? json_decode($question->option->option_text, true)
            : [];

        $correct = optional($question->option)->correct_option;
    @endphp

    <div class="card mb-4">
        <div class="card-body">

            <h5>Soal {{ $index + 1 }}</h5>
            <p>{{ $question->question_text }}</p>

            {{-- PILIHAN GANDA --}}
            @if ($question->question_type === 'multiple_choice')
                @foreach ($options as $i => $opt)
                <div class="form-check">
                    <input class="form-check-input" type="radio" disabled
                        {{ $studentAnswer === $opt['text'] ? 'checked' : '' }}>
                    <label class="form-check-label
                        {{ $assignment->corrected === 'system' && $opt['text'] === $correct ? 'text-success fw-bold' : '' }}">
                        {{ chr(65 + $i) }}. {{ $opt['text'] }}
                    </label>
                </div>
                @endforeach
            @endif

            {{-- ESSAY --}}
            @if ($question->question_type === 'essay')
                <div class="border rounded p-2 bg-light">
                    {{ $studentAnswer }}
                </div>
            @endif

            {{-- HASIL --}}
            <hr>

            @if ($result->status === 'completed')
                <p><strong>Jawaban Benar:</strong> {{ $correct }}</p>
                <p><strong>Poin:</strong> {{ $earned }}</p>
            @else
                <span class="badge bg-warning">Menunggu penilaian guru</span>
            @endif

        </div>
    </div>
    @endforeach

</div>
@endsection
