@extends('layouts.sidebar')

@section('title', 'Manajemen Lowongan Pekerjaan')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manajemen Lowongan Pekerjaan</h1>
    {{-- Tombol ini otomatis berwarna hijau karena class 'btn-primary' --}}
    <a
      href="{{ route('hrd.job_vacancies.create') }}"
      class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Lowongan Baru</a>
  </div>

  @if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table-hover table align-middle">
          {{-- [UBAH] Ganti class 'table-dark' dengan 'table-green' yang akan kita definisikan --}}
          <thead class="table-green">
            <tr>
              <th>No.</th>
              <th>Judul Lowongan</th>
              <th>Status</th>
              <th>Batas Lamaran</th>
              <th>Jumlah Pelamar</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($vacancies as $vacancy)
            <tr>
              <td>{{ $loop->iteration + $vacancies->firstItem() - 1 }}</td>
              <td class="fw-bold">{{ $vacancy->title }}</td>
              <td>
                @if ($vacancy->status == 'open')
                <span class="badge bg-success">Dibuka</span>
                @else
                <span class="badge bg-secondary">Ditutup</span>
                @endif
              </td>
              <td>
                {{ $vacancy->deadline_at ? \Carbon\Carbon::parse($vacancy->deadline_at)->isoFormat('D MMMM YYYY') : '-' }}
              </td>
              <td>
                <a
                  href="#"
                  class="btn btn-sm btn-outline-dark">{{ $vacancy->applications_count }} Pelamar</a>
              </td>
              <td class="text-center">
                <a
                  href="#"
                  class="btn btn-sm btn-info"
                  title="Lihat Pelamar"><i class="bi bi-people-fill"></i></a>
                <a
                  href="{{ route('hrd.job_vacancies.edit', $vacancy) }}"
                  class="btn btn-sm btn-warning"
                  title="Edit"><i class="bi bi-pencil-fill"></i></a>
                <form
                  action="{{ route('hrd.job_vacancies.destroy', $vacancy) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan ini? Semua data lamaran terkait akan ikut terhapus.');">
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="btn btn-sm btn-danger"
                    title="Hapus"><i class="bi bi-trash-fill"></i></button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td
                colspan="6"
                class="py-4 text-center">Belum ada data lowongan pekerjaan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Link Paginasi --}}
      <div class="d-flex justify-content-end">
        {{ $vacancies->links() }}
      </div>
    </div>
  </div>
</div>
@endsection