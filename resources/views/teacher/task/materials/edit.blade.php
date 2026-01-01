@extends('layout-teacher')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">
            Edit Materi – {{ $course->name }}
        </h5>

        <a href="{{ route('teacher.courses.tasks.index', [$class->id, $course->id]) }}"
        class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>

    <form method="POST"
          action="{{ route('teacher.courses.materials.update', [$class->id, $course->id, $material->id]) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Judul Materi</label>
                    <input type="text" name="title"
                           value="{{ old('title', $material->title) }}"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Materi</label>
                    <select name="type" class="form-select" id="typeSelect">
                        <option value="text" {{ $material->type === 'text' ? 'selected' : '' }}>Teks</option>
                        <option value="file" {{ $material->type === 'file' ? 'selected' : '' }}>File</option>
                        <option value="video" {{ $material->type === 'video' ? 'selected' : '' }}>Video</option>
                    </select>
                </div>

                {{-- TEXT --}}
                <div class="mb-3 {{ $material->type !== 'text' ? 'd-none' : '' }}" id="contentField">
                    <label class="form-label">Konten</label>
                    <textarea name="content" rows="5" class="form-control">{{ old('content', $material->content) }}</textarea>
                </div>

                {{-- FILE --}}
                <div class="mb-3 {{ $material->type !== 'file' ? 'd-none' : '' }}" id="fileField">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file" class="form-control">

                    @if($material->file_path)
                        <small class="text-muted d-block mt-1">
                            File saat ini:
                            <a href="{{ asset($material->file_path) }}" target="_blank">Lihat</a>
                        </small>
                    @endif
                </div>

                {{-- VIDEO --}}
                <div class="mb-3 {{ $material->type !== 'video' ? 'd-none' : '' }}" id="videoField">
                    <label class="form-label">URL Video</label>
                    <input type="url" name="video_url"
                           value="{{ old('video_url', $material->video_url) }}"
                           class="form-control">
                </div>

                {{-- DESCRIPTION (FILE & VIDEO) --}}
                <div class="mb-3 {{ in_array($material->type, ['file','video']) ? '' : 'd-none' }}" id="descriptionField">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control"
                        placeholder="Deskripsi singkat materi (opsional)">{{ old('description', $material->description) }}</textarea>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">
                        Update Materi
                    </button>
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

    function toggleFields(type) {
        content.classList.add('d-none');
        file.classList.add('d-none');
        video.classList.add('d-none');
        description.classList.add('d-none');

        if (type === 'text') {
            content.classList.remove('d-none');
        }

        if (type === 'file') {
            file.classList.remove('d-none');
            description.classList.remove('d-none');
        }

        if (type === 'video') {
            video.classList.remove('d-none');
            description.classList.remove('d-none');
        }
    }

    // INIT saat halaman load (EDIT MODE)
    toggleFields(typeSelect.value);

    typeSelect.addEventListener('change', () => {
        toggleFields(typeSelect.value);
    });
</script>
@endsection
