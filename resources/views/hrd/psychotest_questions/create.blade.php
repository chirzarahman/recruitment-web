@extends('layouts.sidebar')
@section('title', 'Tambah Soal Baru')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4">Tambah Soal Baru</h1>
  <div class="card shadow">
    <div class="card-body">
      <form
        action="{{ route('hrd.psychotest_questions.store') }}"
        method="POST">
        @include('hrd.psychotest_questions._form')
      </form>
    </div>
  </div>
</div>
@endsection