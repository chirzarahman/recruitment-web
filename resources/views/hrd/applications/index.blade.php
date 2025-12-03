@extends('layouts.sidebar')

@section('title', 'Daftar Pelamar')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Daftar Pelamar</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- FORM FILTER --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('hrd.applications.index') }}" class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <label for="job_vacancy_id" class="form-label fw-bold">Filter Lowongancsa</label>
                        <select id="job_vacancy_id" name="job_vacancy_id" class="form-select">
                            <option value="">Semua Lowongan</option>
                            @foreach ($vacanciesForFilter as $vacancy)
                                <option value="{{ $vacancy->id }}"
                                    {{ $request->job_vacancy_id == $vacancy->id ? 'selected' : '' }}>
                                    {{ $vacancy->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="status" class="form-label fw-bold">Filter Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach ($statusesForFilter as $status)
                                <option value="{{ $status }}" {{ $request->status == $status ? 'selected' : '' }}
                                    class="text-capitalize">
                                    {{ str_replace('_', ' ', $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end pt-3">
                        <button type="submit" class="btn btn-primary w-100 me-2">Filter</button>
                        <a href="{{ route('hrd.applications.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL DAFTAR PELAMAR --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table-hover table align-middle">
                        {{-- [UBAH] Menggunakan class .table-green yang sudah global --}}
                        <thead class="table-green">
                            <tr>
                                <th>Nama Pelamar</th>
                                <th>Lowongan Dilamar</th>
                                <th>Status Saat Ini</th>
                                <th>Skor Psikotes</th>
                                <th>Tgl Lamar</th>
                                <th class="text-center" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                                <tr>
                                    <td class="fw-bold">{{ $application->user->name }}</td>
                                    <td>{{ $application->jobVacancy->title }}</td>
                                    <td>
                                        @php
                                            $status = $application->status;
                                            $statusText = str_replace('_', ' ', $status);
                                            $badgeColor = 'secondary';
                                            if (in_array($status, ['menunggu_seleksi'])) {
                                                $badgeColor = 'primary';
                                            }
                                            if (in_array($status, ['tes_psikotes'])) {
                                                $badgeColor = 'warning text-dark';
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
                                        @endphp
                                        <span
                                            class="badge bg-{{ $badgeColor }} text-capitalize fs-6">{{ $statusText }}</span>

                                        @if ($application->status === 'tes_psikotes')
                                            @if ($application->testResult)
                                                <small class="d-block text-muted">(Menunggu Review)</small>
                                            @else
                                                <small class="d-block text-muted">(Menunggu Pengerjaan)</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="fw-bold text-primary">{{ $application->testResult->score ?? '-' }}</td>
                                    <td>{{ $application->created_at->isoFormat('D MMM YY') }}</td>
                                    <td class="text-center">
                                        @if (Auth::user()->role === 'hrd')
                                            <a href="{{ route('hrd.applications.ambilDokumen', $application->id) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-file-earmark-text"></i> Dokumen
                                            </a>
                                            @if (in_array($application->status, ['menunggu_seleksi', 'tes_psikotes', 'wawancara_pertama', 'wawancara_kedua']))
                                                <div class="btn-group" role="group">
                                                    <form
                                                        action="{{ route('hrd.applications.updateStatus', $application) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="accept">
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Lolos ke tahap selanjutnya"><i
                                                                class="bi bi-check-lg"></i>
                                                            Accept</button>
                                                    </form>
                                                    <form
                                                        action="{{ route('hrd.applications.updateStatus', $application) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menolak kandidat ini?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Tolak kandidat"><i class="bi bi-x-lg"></i>
                                                            Reject</button>
                                                    </form>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        @else
                                            <a href="{{ route('manager.applications.ambilDokumen', $application->id) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-file-earmark-text"></i> Dokumen
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center">Data pelamar tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const documentsModal = document.getElementById('documentsModal');
            const modalBodyContent = documentsModal.querySelector('#modalBodyContent');
            const loadingState = documentsModal.querySelector('#loadingState');

            documentsModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const fetchUrl = button.getAttribute('data-url');
                // ... (Kode setup state, hapus yang lama, tampilkan loading) ...
                modalBodyContent.innerHTML = '';
                modalBodyContent.style.display = 'none';
                loadingState.style.display = 'block';

                if (!fetchUrl) {
                    /* ... handle error ... */
                    return;
                }

                // Lakukan Permintaan AJAX (Fetch API)
                fetch(fetchUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Jika status 4xx atau 5xx, kita tetap baca JSON-nya
                            return response.json().then(data => {
                                throw new Error(data.html || 'Gagal memuat dokumen. Status: ' +
                                    response.status);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Berhasil: masukkan konten HTML
                        modalBodyContent.innerHTML = data.html;
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        // Tampilkan pesan error di modal
                        modalBodyContent.innerHTML =
                            `<div class="alert alert-danger">Error: ${error.message}</div>`;
                    })
                    .finally(() => {
                        // Sembunyikan loading state dan tampilkan konten
                        loadingState.style.display = 'none';
                        modalBodyContent.style.display = 'block';
                    });
            });
            // ... (Kode hidden.bs.modal) ...
        });
    </script> -->
@endsection
