@extends('layouts.sidebar')

@section('title', 'Dokumen List')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dokumen List</h1>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-hover table align-middle">
                    <thead class="table-green">
                        <tr>
                            <th>Nama Dokumen</th>
                            <th class="text-center" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
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
                        <tr>
                            <td class="fw-bold">
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
                            </td>
                            <td class="text-center">
                                @if ($uploadedDocumentsMap->has($docName))
                                <span class="badge bg-success me-2">Uploaded</span>
                                <a href="{{ asset('storage/' . $uploadedDocumentsMap[$docName]->file_path) }}"
                                    target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                @else
                                <span class="badge bg-danger me-2">Missing</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection