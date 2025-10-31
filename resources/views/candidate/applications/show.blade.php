@extends('layouts.app')

@section('title', 'Detail Lamaran: ' . $application->jobVacancy->title)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Lamaran</h1>
        <a href="{{ route('candidate.profile') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
            Kembali ke Profil</a>
    </div>

    @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: DETAIL LOWONGAN & STATUS --}}
        <div class="col-lg-7 mb-4">
            {{-- KARTU DETAIL LOWONGAN --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Lowongan Dilamar: {{ $application->jobVacancy->title }}</h5>
                </div>
                <div class="card-body">
                    {{-- PEMBARUAN FORMAT TANGGAL --}}
                    <p class="text-muted">Batas Lamaran:
                        {{ $application->jobVacancy->deadline_at ? \Carbon\Carbon::parse($application->jobVacancy->deadline_at)->isoFormat('D MMMM YYYY') : 'Tidak ditentukan' }}
                    </p>
                    <hr>
                    <h6>Deskripsi Pekerjaan:</h6>
                    <div style="white-space: pre-wrap;">{{ $application->jobVacancy->description }}</div>
                </div>
            </div>

            {{-- KARTU STATUS LAMARAN --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Status Lamaran Anda</h5>
                </div>
                <div class="card-body">
                    @php
                    $status = $application->status;
                    $badgeColor = 'secondary';
                    $statusText = '';

                    // LOGIKA BARU UNTUK TAMPILAN STATUS
                    if ($status === 'tes_psikotes') {
                    if ($application->testResult) {
                    $statusText = 'Tes Psikotes (Selesai - Menunggu Review)';
                    $badgeColor = 'primary'; // Warna biru menandakan "sedang diproses"
                    } else {
                    $statusText = 'Tes Psikotes (Menunggu Pengerjaan)';
                    $badgeColor = 'warning text-dark';
                    }
                    } else {
                    $statusText = str_replace('_', ' ', $status);
                    if (in_array($status, ['menunggu_seleksi'])) {
                    $badgeColor = 'primary';
                    }
                    if (in_array($status, ['wawancara_pertama', 'wawancara_kedua'])) {
                    $badgeColor = 'info text-dark';
                    }
                    if ($status === 'diterima') {
                    $badgeColor = 'success';
                    }
                    if ($status === 'ditolak') {
                    $badgeColor = 'danger';
                    }
                    }
                    @endphp
                    <h4><span class="badge bg-{{ $badgeColor }} text-capitalize">{{ $statusText }}</span></h4>

                    {{-- PEMBARUAN FORMAT TANGGAL --}}
                    <p class="text-muted mt-2">Lamaran Anda diajukan pada:
                        {{ $application->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>

                    {{-- LOGIKA BARU: SEMBUNYIKAN TOMBOL JIKA TES SUDAH DIKERJAKAN --}}
                    @if ($application->status === 'tes_psikotes' && !$application->testResult)
                    <div class="alert alert-warning mt-3">
                        <h5 class="alert-heading">Tahap Selanjutnya: Tes Psikotes</h5>
                        <p>Anda diundang untuk mengikuti tes psikotes online. Silakan klik tombol di bawah untuk memulai
                            tes.
                            Pastikan Anda memiliki koneksi internet yang stabil.</p>
                        <hr>
                        <a href="{{ route('psychotest.show', $application) }}" class="btn btn-success fw-bold">Mulai Tes
                            Psikotes Sekarang</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DOKUMEN & FORM UPLOAD (Tidak ada perubahan di sini) --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dokumen Lamaran</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @php
                    // Mapping dokumen yang diharapkan dan statusnya
                    $expectedDocuments = [
                    'Surat Lamaran' => 'surat_lamaran',
                    'CV' => 'cv',
                    'Fotokopi KTP dan KK' => 'ktp_kk',
                    'Fotokopi Ijazah dan Transkrip Nilai' => 'ijazah_transkrip',
                    ];
                    // Mengubah koleksi dokumen terunggah menjadi associative array untuk pencarian mudah
                    $uploadedDocumentsMap = $application->documents->keyBy('document_name');
                    @endphp

                    @foreach ($expectedDocuments as $docName => $fieldName)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i
                                class="bi bi-file-earmark-text {{ $uploadedDocumentsMap->has($docName) ? 'text-success' : 'text-danger' }} me-2"></i>
                            {{ $docName }}
                            @if (in_array($docName, [
                            'Surat Lamaran',
                            'CV',
                            'Fotokopi KTP dan KK',
                            'Fotokopi Ijazah dan
                            Transkrip Nilai',
                            ]))
                            <span class="text-danger">*</span>
                            @endif
                        </div>
                        <div>
                            @if ($uploadedDocumentsMap->has($docName))
                            <span class="badge bg-success me-2">Uploaded</span>
                            <!-- <a href="{{ asset('storage/' . $uploadedDocumentsMap[$docName]->file_path) }}"
                                target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a> -->
                            @else
                            <span class="badge bg-danger me-2">Missing</span>
                            @endif
                        </div>
                    </li>
                    @endforeach

                    {{-- Menampilkan Dokumen Pendukung --}}
                    @if ($uploadedDocumentsMap->has('Dokumen Pendukung'))
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <strong>Dokumen Pendukung Lainnya:</strong>
                    </li>
                    @foreach ($application->documents->filter(
                    fn($doc) => str_starts_with(
                    $doc->document_name,
                    'Dokumen
                    Pendukung',
                    ),
                    ) as $doc)
                    <li class="list-group-item d-flex justify-content-between align-items-center ps-5">
                        <span><i class="bi bi-paperclip text-primary me-2"></i>{{ $doc->document_name }}</span>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                            class="btn btn-sm btn-outline-secondary">Lihat</a>
                    </li>
                    @endforeach
                    @endif
                </ul>

                {{-- Tampilkan form upload HANYA JIKA status 'belum_lengkap' --}}
                @if ($application->status === 'belum_lengkap')
                <div class="card-footer bg-white">
                    <h6 class="mb-3">Unggah Dokumen</h6>
                    <form action="{{ route('candidate.applications.storeDocument', $application) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file_surat_lamaran" class="form-label">Surat Lamaran (PDF, JPG, PNG)</label>
                            <input id="file_surat_lamaran"
                                class="form-control @error('surat_lamaran') is-invalid @enderror" type="file"
                                name="surat_lamaran">
                            @error('surat_lamaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_cv" class="form-label">CV (PDF, DOC, DOCX)</label>
                            <input id="file_cv" class="form-control @error('cv') is-invalid @enderror" type="file"
                                name="cv">
                            @error('cv')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_ktp_kk" class="form-label">Fotokopi KTP dan KK</label>
                            <input id="file_ktp_kk" class="form-control @error('ktp_kk') is-invalid @enderror"
                                type="file" name="ktp_kk">
                            @error('ktp_kk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_ijazah_transkrip" class="form-label">Fotokopi Ijazah dan Transkrip
                                Nilai</label>
                            <input id="file_ijazah_transkrip"
                                class="form-control @error('ijazah_transkrip') is-invalid @enderror" type="file"
                                name="ijazah_transkrip">
                            @error('ijazah_transkrip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_skck" class="form-label">Fotokopi SKCK (Opsional)</label>
                            <input id="file_skck" class="form-control @error('skck') is-invalid @enderror" type="file"
                                name="skck">
                            @error('skck')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="file_dokumen_pendukung" class="form-label">Dokumen Pendukung (Opsional,
                                multiple)</label>
                            <input id="file_dokumen_pendukung"
                                class="form-control @error('dokumen_pendukung.*') is-invalid @enderror" type="file"
                                name="dokumen_pendukung[]" multiple>
                            <small class="form-text text-muted">Contoh: Surat pengalaman kerja, sertifikat pelatihan,
                                dll.</small>
                            @error('dokumen_pendukung.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary">Unggah Dokumen</button>
                        </div>
                        <p class="text-muted mb-0 mt-2 text-center"><small>Maks: 2MB per file. Format: PDF, JPG, JPEG,
                                PNG (untuk
                                pendukung: DOC, DOCX juga).</small></p>
                    </form>
                </div>
                @else
                <div class="card-footer bg-light text-center">
                    <p class="text-muted mb-0">Dokumen tidak dapat diunggah pada status lamaran ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection