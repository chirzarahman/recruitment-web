@extends('layouts.sidebar')

@section('title', 'Tambah Lowongan Baru')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Lowongan Pekerjaan Baru</h1>
    <a
      href="{{ route('hrd.job_vacancies.index') }}"
      class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
  </div>

  <div class="card mb-4 shadow">
    <div class="card-header py-3">
      <h6 class="fw-bold text-primary m-0">Formulir Lowongan Pekerjaan</h6>
    </div>
    <div class="card-body">
      <form
        action="{{ route('hrd.job_vacancies.store') }}"
        method="POST">
        @include('hrd.job_vacancies._form')
      </form>
    </div>
  </div>
</div>
@endsection