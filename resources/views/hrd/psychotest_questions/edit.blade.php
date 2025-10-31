@extends('layouts.sidebar')
@section('title', 'Edit Soal')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4">Edit Soal</h1>
  <div class="card shadow">
    <div class="card-body">
      <form
        action="{{ route('hrd.psychotest_questions.update', $question) }}"
        method="POST">
        @method('PUT')
        @php
        // Cari index dari jawaban yang benar untuk pre-check radio button
        $correct_answer_index = $question->options->search(fn($option) => $option->is_correct);
        @endphp
        @include('hrd.psychotest_questions._form', [
        'question' => $question,
        'correct_answer_index' => $correct_answer_index,
        ])
      </form>
    </div>
  </div>
</div>
@endsection