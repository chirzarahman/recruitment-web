<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CandidateProfile;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil kandidat.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Ambil data user yang sedang login beserta profilnya
        $user = Auth::user()->load('candidateProfile');

        return view('candidate.profile.edit', compact('user'));
    }

    /**
     * Memperbarui profil kandidat di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'place_of_birth' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:laki-laki,perempuan',
            'phone_number' => 'required|string|min:10|max:15',
            'address' => 'required|string',
        ]);

        $profile = Auth::user()->candidateProfile;

        $validatedData['is_complete'] = true;
        $profile->update($validatedData);
        return redirect()->route('candidate.profile')->with('status', 'Profil Anda telah berhasil diperbarui dan dilengkapi!');
    }

    /**
     * Menampilkan form untuk mengunggah/memperbarui CV utama.
     *
     * @return \Illuminate\View\View
     */
    public function editResume()
    {
        return view('candidate.profile.edit-resume');
    }

    /**
     * Memproses file CV yang diunggah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateResume(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048', // Maksimal 2MB
        ]);

        $profile = Auth::user()->candidateProfile;

        // 2. Hapus file CV lama jika ada
        if ($profile->resume_path) {
            Storage::disk('public')->delete($profile->resume_path);
        }

        // 3. Simpan file CV yang baru
        // Laravel akan otomatis membuat nama file unik
        $path = $request->file('resume')->store('resumes', 'public');

        // 4. Update path file di database
        $profile->update([
            'resume_path' => $path
        ]);

        // 5. Redirect kembali ke halaman profil dengan pesan sukses
        return redirect()->route('candidate.profile')->with('status', 'CV Utama berhasil diperbarui!');
    }
}