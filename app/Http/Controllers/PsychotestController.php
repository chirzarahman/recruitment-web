<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\PsychotestOption;
use App\Models\PsychotestQuestion;
use App\Models\TestResult;

class PsychotestController extends Controller
{
    /**
     * Menampilkan halaman tes psikotes.
     */
    public function show(Application $application)
    {
        // Otorisasi: Pastikan kandidat hanya bisa mengakses tes untuk lamarannya sendiri
        if (Auth::id() !== $application->user_id) {
            abort(403);
        }

        // Cek apakah status lamaran sudah benar dan tes belum pernah diambil
        if ($application->status !== 'tes_psikotes' || $application->testResult) {
            return redirect()->route('candidate.profile')->with('error', 'Anda tidak dapat mengakses halaman ini.');
        }

        // Ambil 10 soal secara acak sebagai contoh
        $questions = PsychotestQuestion::with('options')->inRandomOrder()->get();

        return view('psychotest.show', compact('questions', 'application'));
    }

    /**
     * Menyimpan hasil tes psikotes.
     */
    public function store(Request $request, Application $application)
    {
        // 1. Otorisasi & Validasi Keamanan
        if (Auth::id() !== $application->user_id || $application->status !== 'tes_psikotes' || $application->testResult) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $userAnswers = $request->input('answers', []);

        if (empty($userAnswers)) {
            return back()->with('error', 'Anda harus menjawab setidaknya satu pertanyaan.');
        }

        // 2. Ambil Kunci Jawaban dari Database
        // Dapatkan ID dari semua pertanyaan yang dijawab oleh user
        $questionIds = array_keys($userAnswers);

        // Buat "peta" kunci jawaban: [question_id => correct_option_id]
        $correctOptions = \App\Models\PsychotestOption::whereIn('psychotest_question_id', $questionIds)
            ->where('is_correct', true)
            ->pluck('id', 'psychotest_question_id');

        // 3. Hitung Skor
        $correctCount = 0;
        foreach ($userAnswers as $questionId => $chosenOptionId) {
            // Cek apakah jawaban user cocok dengan kunci jawaban
            if (isset($correctOptions[$questionId]) && $correctOptions[$questionId] == $chosenOptionId) {
                $correctCount++;
            }
        }

        // 4. Konversi Skor ke Skala 0-100 (agar mudah dibaca HRD)
        $totalQuestions = count($questionIds);
        $finalScore = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;

        // 5. Simpan Hasil Tes
        TestResult::create([
            'application_id' => $application->id,
            'score' => $finalScore,
            'answers' => json_encode($userAnswers), // Simpan jawaban user (opsional tapi bagus untuk audit)
            'completed_at' => now(),
            'token' => \Str::random(32), // Generate token unik untuk hasil tes
        ]);

        // 6. Redirect (Status TIDAK diubah)
        return redirect()->route('candidate.applications.show', $application)
            ->with('status', 'Tes psikotes telah selesai. Hasil Anda telah kami rekam. Harap tunggu informasi selanjutnya dari tim HRD.');
    }
}