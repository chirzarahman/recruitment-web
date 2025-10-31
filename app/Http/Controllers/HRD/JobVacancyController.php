<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JobVacancyController extends Controller
{
    /**
     * Menampilkan daftar semua lowongan pekerjaan untuk HRD.
     */
    public function index()
    {
        $vacancies = JobVacancy::withCount('applications')->latest()->paginate(15);
        return view('hrd.job_vacancies.index', compact('vacancies'));
    }

    /**
     * Menampilkan form untuk membuat lowongan baru.
     */
    public function create()
    {
        return view('hrd.job_vacancies.create');
    }

    /**
     * Menyimpan lowongan baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,closed',
            'deadline_at' => 'nullable|date|after_or_equal:today',
        ]);

        JobVacancy::create($validatedData);

        return redirect()->route('hrd.job_vacancies.index')->with('success', 'Lowongan pekerjaan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit lowongan.
     */
    public function edit(JobVacancy $lowongan)
    {
        return view('hrd.job_vacancies.edit', [
            'vacancy' => $lowongan,
        ]);
    }

    /**
     * Memperbarui data lowongan di database.
     */
    public function update(Request $request, JobVacancy $lowongan)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:open,closed',
            'deadline_at' => 'nullable|date',
        ]);

        $lowongan->update($validatedData);

        return redirect()->route('hrd.job_vacancies.index')->with('success', 'Lowongan pekerjaan berhasil diperbarui.');
    }

    /**
     * Menghapus lowongan dari database.
     */
    public function destroy(JobVacancy $lowongan)
    {
        // Karena kita sudah mengatur onDelete('cascade') di migrasi,
        // semua lamaran yang terkait dengan lowongan ini akan ikut terhapus.
        $lowongan->delete();

        return redirect()->route('hrd.job_vacancies.index')->with('success', 'Lowongan pekerjaan berhasil dihapus.');
    }
}