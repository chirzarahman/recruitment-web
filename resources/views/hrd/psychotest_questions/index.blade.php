@extends('layouts.sidebar')
@section('title', 'Manajemen Soal Psikotes')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Soal Psikotes</h1>
        <a href="{{ route('hrd.psychotest_questions.create') }}" class="btn btn-primary"><i
                class="bi bi-plus-circle-fill me-2"></i>Tambah Soal Baru</a>
    </div>
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            @forelse ($questions as $question)
            <div class="border-bottom mb-4 pb-3">
                <div class="d-flex justify-content-between">
                    <p class="fw-bold">{{ $loop->iteration + $questions->firstItem() - 1 }}.
                        {{ $question->question_text }}</p>
                    <div>
                        <a href="{{ route('hrd.psychotest_questions.edit', $question) }}"
                            class="btn btn-sm btn-warning"><i class="bi bi-pencil-fill"></i> Edit</a>
                        <form action="{{ route('hrd.psychotest_questions.destroy', $question) }}" method="POST"
                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i>
                                Hapus</button>
                        </form>
                    </div>
                </div>
                <ul class="list-unstyled ps-4">
                    @foreach ($question->options as $option)
                    <li class="{{ $option->is_correct ? 'text-success fw-bold' : '' }}">
                        <i class="bi {{ $option->is_correct ? 'bi-check-circle-fill' : 'bi-circle' }} me-2"></i>
                        {{ $option->option_text }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @empty
            <p class="text-center">Belum ada soal yang dibuat.</p>
            @endforelse
            <!-- <div class="d-flex justify-content-end">
                {{ $questions->links() }}
            </div> -->
        </div>
    </div>
</div>
@endsection