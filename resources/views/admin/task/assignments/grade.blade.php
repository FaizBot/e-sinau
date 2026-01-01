@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="mb-1">{{ $assignment->title }}</h5>
                <small class="text-muted">
                    Penilaian Guru • {{ $student->user->name }} ({{ $student->nis }})
                </small>
            </div>

            <a href="{{ route('admin.assignment.progress', [
                'class'      => $class->id,
                'course'     => $course->id,
                'assignment' => $assignment->id,
            ]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>

    <form method="POST"
        action="{{ route('admin.assignment.grade.update', [$class, $course, $assignment, $student->nis]) }}">
        @csrf
        @method('PUT')

        @php
            $savedPoints = json_decode($result->points ?? '{}', true);
        @endphp

        @foreach ($questions as $index => $question)

            @php
                $studentAnswer = $studentAnswers[$question->id] ?? null;
                $score = $savedPoints[$question->id] ?? 0;

                // pastikan options array
                if (is_array($question->option?->option_text)) {
                    $options = $question->option->option_text;
                } else {
                    $options = json_decode($question->option?->option_text ?? '{}', true);
                }

                $correct = $question->option?->correct_option;
            @endphp

            <div class="card mb-3">
                <div class="card-header">
                    <strong>Pertanyaan {{ $index + 1 }}</strong>
                    <span class="badge bg-secondary ms-2">
                        {{ strtoupper($question->question_type) }}
                    </span>
                </div>

                <div class="card-body">

                    {{-- SOAL --}}
                    <p>{!! nl2br(e($question->question_text)) !!}</p>

                    {{-- ========================= --}}
                    {{-- PILIHAN GANDA --}}
                    {{-- ========================= --}}
                    @if ($question->question_type === 'multiple_choice')

                        <ul class="list-group mb-2">
                            @foreach ($options as $i => $opt)
                                @php
                                    $optionText = is_array($opt) ? ($opt['text'] ?? '-') : $opt;
                                    $label = chr(65 + $i); // A, B, C
                                @endphp

                                <li class="list-group-item
                                    @if ($studentAnswer === $optionText)
                                        {{ $optionText === $correct
                                            ? 'list-group-item-success'
                                            : 'list-group-item-danger'
                                        }}
                                    @endif
                                ">
                                    <strong>{{ $label }}.</strong> {{ $optionText }}

                                    @if ($optionText === $correct)
                                        <span class="badge bg-success ms-2">Kunci</span>
                                    @endif
                                </li>
                            @endforeach
                            </ul>

                        <small class="text-muted">
                            Jawaban siswa:
                            <strong>{{ $studentAnswer ?? '-' }}</strong>
                        </small>

                        {{-- WAJIB: kirim points meski otomatis --}}
                        <input type="hidden"
                            name="points[{{ $question->id }}]"
                            value="{{ $score }}">

                    {{-- ========================= --}}
                    {{-- ESSAY --}}
                    {{-- ========================= --}}
                    @else

                        <div class="border rounded p-3 bg-light mb-3">
                            {!! nl2br(e($studentAnswer ?? '-')) !!}
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">
                                    Nilai (maks {{ $question->point }})
                                </label>
                                <input type="number"
                                    name="points[{{ $question->id }}]"
                                    class="form-control"
                                    value="{{ $score }}"
                                    min="0"
                                    max="{{ $question->point }}"
                                    required>
                            </div>
                        </div>

                    @endif

                </div>
            </div>

        @endforeach

        <div class="text-end mt-4">
            <button class="btn btn-primary">
                <i class="bx bx-save"></i> Simpan Penilaian
            </button>
        </div>

    </form>

</div>
@endsection
