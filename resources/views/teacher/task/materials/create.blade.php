@extends('layout-teacher')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">
            Tambah Materi – {{ $course->name }}
        </h5>

        <a href="{{ route('teacher.courses.tasks.index', [$class->id, $course->id]) }}"
        class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('teacher.courses.materials.store', [$class->id, $course->id]) }}"
          enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Judul Materi</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Materi</label>
                    <select name="type" class="form-select" id="typeSelect">
                        <option value="text">Teks</option>
                        <option value="file">File</option>
                        <option value="video">Video</option>
                    </select>
                </div>

                <div class="mb-3" id="contentField">
                    <label class="form-label">Konten</label>
                    <textarea name="content" rows="5" class="form-control"></textarea>
                </div>

                <div class="mb-3 d-none" id="fileField">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file" class="form-control">
                </div>

                <div class="mb-3 d-none" id="videoField">
                    <label class="form-label">URL Video</label>
                    <input type="url" name="video_url" class="form-control">
                </div>

                <div class="mb-3 d-none" id="descriptionField">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control"
                            placeholder="Deskripsi singkat materi (opsional)"></textarea>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">Simpan Materi</button>
                </div>

            </div>
        </div>
    </form>

</div>

<script>
    const typeSelect   = document.getElementById('typeSelect');
    const content      = document.getElementById('contentField');
    const file         = document.getElementById('fileField');
    const video        = document.getElementById('videoField');
    const description  = document.getElementById('descriptionField');

    function resetFields() {
        content.classList.add('d-none');
        file.classList.add('d-none');
        video.classList.add('d-none');
        description.classList.add('d-none');
    }

    typeSelect.addEventListener('change', () => {
        resetFields();

        if (typeSelect.value === 'text') {
            content.classList.remove('d-none');
        }

        if (typeSelect.value === 'file') {
            file.classList.remove('d-none');
            description.classList.remove('d-none');
        }

        if (typeSelect.value === 'video') {
            video.classList.remove('d-none');
            description.classList.remove('d-none');
        }
    });
</script>
@endsection
