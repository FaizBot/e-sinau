@extends('layout-student')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-1">{{ $assignment->title }}</h5>
            <small class="text-muted">
                {{ $course->name }} • Kelas {{ $class->name }}
            </small>
        </div>
    </div>

    <form action="{{ route('student.assignment.submit', [$class->id, $course->id, $assignment->id]) }}"
          method="POST">
        @csrf

        @foreach ($assignment->questions as $index => $question)
            <div class="card mb-3">
                <div class="card-body">

                    <strong>Soal {{ $index + 1 }}</strong>
                    <p class="mt-2">{{ $question->question_text }}</p>

                    {{-- PILIHAN GANDA --}}
                    @if ($question->question_type === 'multiple_choice' && $question->option)
                        @php
                            $rawOptions = $question->option->option_text;
                            $options = is_string($rawOptions)
                                ? json_decode($rawOptions, true) ?? []
                                : $rawOptions;
                        @endphp

                        @foreach ($options as $i => $opt)
                            <div class="form-check mb-1">
                                <input class="form-check-input"
                                       type="radio"
                                       name="answers[{{ $question->id }}]"
                                       value="{{ $opt['text'] ?? '' }}"
                                       id="q{{ $question->id }}_{{ $i }}">

                                <label class="form-check-label"
                                       for="q{{ $question->id }}_{{ $i }}">
                                    {{ chr(65 + $i) }}. {{ $opt['text'] ?? '-' }}
                                </label>
                            </div>
                        @endforeach
                    @endif

                    {{-- ESSAY --}}
                    @if ($question->question_type === 'essay')
                        <textarea name="answers[{{ $question->id }}]"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Jawaban kamu..."></textarea>
                    @endif

                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('student.courses.tasks.index', [$class->id, $course->id]) }}"
            class="btn btn-secondary">
                ← Kembali
            </a>

            <button type="submit" class="btn btn-primary">
                Kirim Jawaban
            </button>
        </div>

    </form>
</div>
@endsection
