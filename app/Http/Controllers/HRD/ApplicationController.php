<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationStatusUpdated;
use App\Models\Application;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApplicationController extends Controller
{
    public function getDocuments(Application $application)
    {
        // HAPUS SEMENTARA Otorisasi jika itu yang menyebabkan error 403 atau 500
        // if (Auth::user()->level !== 'hrd') { abort(403, 'Akses ditolak...'); }

        // Coba dd() di sini untuk memastikan Anda mendapat objek Application yang benar
        // dd($application); 

        // Eager Load data dokumen dan user
        $application->load(['documents', 'user']);

        // Coba dd() di sini untuk memastikan relasi documents sudah termuat
        // dd($application->documents); // <--- INI PENTING!

        // Jika dd($application->documents) berhasil menampilkan koleksi dokumen,
        // maka masalahnya ada di file Blade Anda.

        // Hapus dd() di atas dan kembalikan kode aslinya jika sudah selesai debug.
        $documents = $application->documents;
        return view('hrd.applications.dokumen_list', compact('application', 'documents'));
    }
    
    /**
     * Menampilkan daftar semua pelamar dengan filter.
     */
    public function index(Request $request)
    {
        // Query dasar untuk mengambil aplikasi dengan relasi yang dibutuhkan
        $query = Application::with(['user.candidateProfile', 'jobVacancy', 'testResult'])->latest();

        // Terapkan filter berdasarkan lowongan pekerjaan
        if ($request->filled('job_vacancy_id')) {
            $query->where('job_vacancy_id', $request->job_vacancy_id);
        }

        // Terapkan filter berdasarkan status lamaran
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ambil hasil dengan paginasi, dan pertahankan query string saat berpindah halaman
        $applications = $query->paginate(15)->withQueryString();

        // Data untuk mengisi dropdown filter
        $vacanciesForFilter = JobVacancy::where('status', 'open')->orderBy('title')->get();
        $statusesForFilter = [
            'menunggu_seleksi',
            'tes_psikotes',
            'wawancara_pertama',
            'wawancara_kedua',
            'diterima',
            'ditolak'
        ];

        return view('hrd.applications.index', compact('applications', 'vacanciesForFilter', 'statusesForFilter', 'request'));
    }

    /**
     * Memperbarui status lamaran (Accept / Reject).
     */
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate(['action' => 'required|in:accept,reject']);

        $newStatus = '';

        if ($request->action === 'reject') {
            $newStatus = 'ditolak';
        } else { // Jika action adalah 'accept'
            $currentStatus = $application->status;
            switch ($currentStatus) {
                case 'menunggu_seleksi':
                    $newStatus = 'tes_psikotes';
                    break;
                case 'tes_psikotes':
                    $newStatus = 'wawancara_pertama';
                    break;
                case 'wawancara_pertama':
                    $newStatus = 'wawancara_kedua';
                    break;
                case 'wawancara_kedua':
                    $newStatus = 'diterima';
                    break;
                default:
                    return back()->with('error', 'Aksi tidak valid untuk status saat ini.');
            }
        }

        // Update status di database
        $application->update(['status' => $newStatus]);

        // 3. Picu pengiriman email JIKA statusnya adalah salah satu yang kita inginkan
        if (in_array($newStatus, ['tes_psikotes', 'wawancara_pertama', 'wawancara_kedua', 'diterima', 'ditolak'])) {
            // Kita perlu me-refresh model untuk mendapatkan data terbaru (termasuk status baru)
            $application->refresh();
            Mail::to($application->user->email)->send(new ApplicationStatusUpdated($application));
        }

        return back()->with('success', 'Status lamaran berhasil diperbarui dan notifikasi email telah dijadwalkan untuk dikirim.');
    }
}