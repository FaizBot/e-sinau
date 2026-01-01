@extends('layout-teacher')

@section('content')
<div class="container-fluid py-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-semibold mb-1">
                {{ $material->title }}
            </h5>
            <small class="text-muted">
                {{ $course->name }} • {{ $class->name }}
            </small>
        </div>

        <a href="{{ route('teacher.courses.tasks.index', [$class->id, $course->id]) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>

    {{-- ================= CARD ================= --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- ===== BADGE TYPE & STATUS ===== --}}
            <div class="mb-3">
                <span class="badge bg-label-primary text-uppercase">
                    {{ $material->type }}
                </span>

                @if(!$material->is_published)
                    <span class="badge bg-label-warning ms-2">
                        Draft
                    </span>
                @endif
            </div>

            {{-- ================= TEXT ================= --}}
            @if($material->type === 'text')
                <div class="lh-lg text-dark">
                    {!! nl2br(e($material->content)) !!}
                </div>
            @endif

            {{-- ================= FILE ================= --}}
            @if($material->type === 'file')

                {{-- DESKRIPSI --}}
                @if($material->description)
                    <p class="text-muted mb-3">
                        {{ $material->description }}
                    </p>
                @endif

                {{-- DOWNLOAD BUTTON --}}
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ asset($material->file_path) }}"
                       class="btn btn-sm btn-primary"
                       download>
                        Download File <i class="bx bx-download"></i>
                    </a>
                </div>

                {{-- PREVIEW --}}
                @if(Str::endsWith($material->file_path, '.pdf'))
                    <iframe
                        src="{{ asset($material->file_path) }}"
                        width="100%"
                        height="600"
                        class="rounded border"
                        loading="lazy">
                    </iframe>
                @else
                    <div class="text-center py-5 border rounded bg-light">
                        <i class="bx bx-file fs-1 text-primary mb-2"></i>
                        <p class="mb-1 fw-semibold">
                            File tidak dapat dipratinjau
                        </p>
                        <small class="text-muted">
                            Silakan unduh file untuk melihat isinya
                        </small>
                    </div>
                @endif
            @endif

            {{-- ================= VIDEO ================= --}}
            @if($material->type === 'video')

                {{-- DESKRIPSI --}}
                @if($material->description)
                    <p class="text-muted mb-3">
                        {{ $material->description }}
                    </p>
                @endif

                <div class="ratio ratio-16x9 rounded overflow-hidden border">
                    <iframe
                        src="{{ youtubeEmbed($material->video_url) }}"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

{{-- ================= HELPER ================= --}}
@php
    function youtubeEmbed($url) {

        // youtu.be/VIDEO_ID
        if (Str::contains($url, 'youtu.be')) {
            return 'https://www.youtube.com/embed/' .
                Str::before(Str::after($url, 'youtu.be/'), '?');
        }

        // youtube.com/watch?v=VIDEO_ID
        if (Str::contains($url, 'watch?v=')) {
            return 'https://www.youtube.com/embed/' .
                Str::before(Str::after($url, 'watch?v='), '&');
        }

        return $url;
    }
@endphp
