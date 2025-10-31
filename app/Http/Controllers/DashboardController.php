<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data untuk Kartu Ringkasan (Summary Cards)
        $openVacanciesCount = JobVacancy::where('status', 'open')->count();
        $totalApplicantsCount = Application::count();
        $newApplicantsTodayCount = Application::whereDate('created_at', today())->count();

        // 2. Data Pelamar yang Memerlukan Aksi
        // Ambil jumlah pelamar per status yang relevan untuk HRD
        $applicantsByStatus = Application::whereIn('status', [
            'menunggu_seleksi',
            'tes_psikotes',
            'wawancara_pertama',
            'wawancara_kedua'
        ])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status'); // Hasil: ['menunggu_seleksi' => 10, 'tes_psikotes' => 5]

        // 3. Data Top 5 Lowongan Paling Populer (Fun Fact)
        $topVacancies = JobVacancy::withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'openVacanciesCount',
            'totalApplicantsCount',
            'newApplicantsTodayCount',
            'applicantsByStatus',
            'topVacancies'
        ));
    }
}