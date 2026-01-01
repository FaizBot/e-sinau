@extends('layout-admin')

@section('content')
<div class="container-fluid py-4">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Tambah Tugas – {{ $course->name }}
            </h5>
            <small class="text-muted">
                Kelas {{ $class->name }}
            </small>
            <a href="{{ route('admin.courses.tasks.index', [$class->id, $course->id]) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.assignment.store', [$course->id, $class->id]) }}"
                method="POST">
                @csrf

                {{-- ================= BASIC INFO ================= --}}
                <div class="mb-3">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Masukkan judul tugas"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description"
                              class="form-control"
                              rows="3"
                              placeholder="Deskripsi singkat tugas (opsional)"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Koreksi</label>
                    <select name="corrected" class="form-select" required>
                        <option value="teacher">Koreksi Manual oleh Guru</option>
                        <option value="system">Koreksi Otomatis oleh Sistem</option>
                    </select>
                </div>

                <hr>

                {{-- ================= QUESTIONS ================= --}}
                <h5 class="mb-3">Daftar Soal</h5>

                <div id="question-container"></div>

                <button type="button"
                        class="btn btn-sm btn-success mb-3"
                        onclick="addQuestion()">
                    <i class="bx bx-plus"></i> Tambah Soal
                </button>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        Simpan Tugas
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
let questionIndex = 0;
let optionCounter = {}; // simpan counter per soal

function addQuestion() {
    optionCounter[questionIndex] = 0;

    const container = document.getElementById('question-container');

    const html = `
    <div class="card border p-3 mb-4" id="question-${questionIndex}">
        <div class="d-flex justify-content-between mb-2">
            <strong>Pertanyaan ${questionIndex + 1}</strong>
            <button type="button"
                    class="btn btn-sm btn-outline-danger"
                    onclick="removeQuestion(${questionIndex})">
                Hapus
            </button>
        </div>

        <div class="mb-2">
            <label class="form-label">Pertanyaan</label>
            <input type="text"
                   name="questions[${questionIndex}][text]"
                   class="form-control"
                   required>
        </div>

        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Nilai</label>
                <input type="number"
                       name="questions[${questionIndex}][point]"
                       class="form-control"
                       value="1"
                       min="1"
                       required>
            </div>

            <div class="col-md-8 mb-2">
                <label class="form-label">Jenis Soal</label>
                <select name="questions[${questionIndex}][question_type]"
                        class="form-select"
                        onchange="toggleOptionBox(this, ${questionIndex})">
                    <option value="multiple_choice">Pilihan Ganda</option>
                    <option value="essay">Essay</option>
                </select>
            </div>
        </div>

        <div id="options-box-${questionIndex}">
            <label class="form-label">Opsi Jawaban</label>
            <div id="options-${questionIndex}"></div>
            <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    onclick="addOption(${questionIndex})">
                + Tambah Opsi
            </button>
        </div>
    </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    addOption(questionIndex);
    addOption(questionIndex);

    questionIndex++;
}

function addOption(qIdx) {
    const optContainer = document.getElementById(`options-${qIdx}`);
    const optId = optionCounter[qIdx]++;

    const optHtml = `
    <div class="input-group mb-2" id="q-${qIdx}-opt-${optId}">
        <input type="text"
               name="questions[${qIdx}][options][${optId}][text]"
               class="form-control"
               placeholder="Teks jawaban"
               required>

        <span class="input-group-text bg-white">
            <label class="d-flex align-items-center m-0" style="cursor:pointer;">
                <input class="form-check-input me-2"
                    type="radio"
                    name="questions[${qIdx}][correct_option]"
                    value="${optId}">
                Benar
            </label>
        </span>

        <button type="button"
                class="btn btn-outline-danger"
                onclick="removeOption(${qIdx}, ${optId})">
            🗑
        </button>
    </div>
    `;

    optContainer.insertAdjacentHTML('beforeend', optHtml);
}

function removeQuestion(idx) {
    document.getElementById(`question-${idx}`)?.remove();
}

function removeOption(qIdx, optId) {
    document.getElementById(`q-${qIdx}-opt-${optId}`)?.remove();
}

function toggleOptionBox(select, idx) {
    const box = document.getElementById(`options-box-${idx}`);
    box.style.display = select.value === 'essay' ? 'none' : 'block';
}
</script>

@endsection
