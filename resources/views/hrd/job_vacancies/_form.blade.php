@csrf
<div class="mb-3">
    <label for="title" class="form-label">Judul Lowongan</label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
        value="{{ old('title', $vacancy->title ?? '') }}" required>
    @error('title')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Deskripsi Pekerjaan</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
        rows="8" required>{{ old('description', $vacancy->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="open" {{ old('status', $vacancy->status ?? '') == 'Open' ? 'selected' : '' }}>Dibuka</option>
            <option value="closed" {{ old('status', $vacancy->status ?? '') == 'Closed' ? 'selected' : '' }}>Ditutup
            </option>
        </select>
        @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="deadline_at" class="form-label">Batas Lamaran (Opsional)</label>
        <input type="date" class="form-control @error('deadline_at') is-invalid @enderror" id="deadline_at"
            name="deadline_at"
            value="{{ old('deadline_at', isset($vacancy->deadline_at) ? \Carbon\Carbon::parse($vacancy->deadline_at)->format('Y-m-d') : '') }}">
        @error('deadline_at')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('hrd.job_vacancies.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan Lowongan</button>
</div>