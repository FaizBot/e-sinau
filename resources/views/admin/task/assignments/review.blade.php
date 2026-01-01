@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="mb-1">{{ $assignment->title }}</h5>
                <small class="text-muted">
                    Review Jawaban • {{ $student->user->name }} ({{ $student->nis }})
                </small>
            </div>

            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- INFO --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted">Status</small><br>
                    <span class="badge {{ $result?->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                        {{ $result?->status === 'completed' ? 'Selesai' : 'Belum Dinilai' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted">Metode Penilaian</small><br>
                    <span class="badge bg-info text-dark text-capitalize">
                        {{ $assignment->corrected }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted">Nilai Akhir</small><br>
                    <h4 class="mb-0">{{ $result?->total_score ?? '-' }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- SOAL & JAWABAN --}}
    @foreach ($questions as $index => $question)
        @php
            $studentAnswer = $studentAnswers[$question->id] ?? null;
            $earned = $points[$question->id] ?? 0;

            $options = [];
            if (is_string(optional($question->option)->option_text)) {
                $options = json_decode($question->option->option_text, true) ?? [];
            }

            $correct = optional($question->option)->correct_option;
        @endphp

        <div class="card mb-3">
            <div class="card-header">
                <strong>Pertanyaan {{ $index + 1 }}</strong>
                <span class="float-end text-muted">
                    Poin: {{ $earned }} / {{ $question->point }}
                </span>
            </div>

            <div class="card-body">

                {{-- SOAL --}}
                <p>{!! nl2br(e($question->question_text)) !!}</p>

                {{-- PILIHAN GANDA --}}
                @if ($question->question_type === 'multiple_choice')
                    <ul class="list-group mb-2">
                        @foreach ($options as $i => $opt)
                            @php
                                $label = chr(65 + $i);
                                $isStudent = $studentAnswer === $opt['text'];
                                $isCorrect = $opt['text'] === $correct;
                            @endphp

                            <li class="list-group-item
                                {{ $isCorrect ? 'list-group-item-success' : '' }}
                                {{ $isStudent && !$isCorrect ? 'list-group-item-danger' : '' }}
                            ">
                                <strong>{{ $label }}.</strong> {{ $opt['text'] }}

                                @if ($isCorrect)
                                    <span class="badge bg-success ms-2">Kunci</span>
                                @endif

                                @if ($isStudent)
                                    <span class="badge bg-primary ms-2">Jawaban Siswa</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <small class="text-muted">
                        Jawaban siswa:
                        <strong>{{ $studentAnswer ?? '-' }}</strong>
                    </small>
                @endif

                {{-- ESSAY --}}
                @if ($question->question_type === 'essay')
                    <div class="border rounded p-3 bg-light mb-2">
                        {!! nl2br(e($studentAnswer ?? '-')) !!}
                    </div>

                    @if ($assignment->corrected === 'teacher')
                        <div class="mt-2">
                            <label class="form-label">
                                Nilai (maks {{ $question->point }})
                            </label>
                            <input type="number"
                                class="form-control form-control-sm"
                                value="{{ $earned }}"
                                max="{{ $question->point }}"
                                disabled>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    @endforeach

</div>
@endsection
