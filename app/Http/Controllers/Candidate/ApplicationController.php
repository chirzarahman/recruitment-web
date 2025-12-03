<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    /**
     * Menampilkan detail dari satu lamaran spesifik.
     * Menggunakan Route Model Binding untuk mengambil $application secara otomatis.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\View\View
     */
    public function show(Application $application)
    {
        // 1. Otorisasi menggunakan Policy yang sudah dibuat
        Gate::authorize('view', $application);

        // 2. Eager load relasi yang dibutuhkan untuk ditampilkan di view
        $application->load(['jobVacancy', 'documents']);

        // 3. Kirim data ke view
        return view('candidate.applications.show', compact('application'));
    }

    /**
     * Menyimpan dokumen yang diunggah untuk lamaran spesifik.
     * Dapat mengunggah satu per satu atau beberapa sekaligus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDocument(Request $request, Application $application)
    {
        // Pastikan user adalah pemilik aplikasi (via Policy)
        Gate::authorize('view', $application); 

        // Hanya izinkan upload jika status 'belum_lengkap'
        if ($application->status !== 'belum_lengkap') {
            return back()->with('error', 'Anda tidak dapat mengunggah dokumen lagi pada tahap ini.');
        }

        // Definisi dokumen wajib dan opsional
        $requiredDocs = [
            'surat_lamaran' => 'Surat Lamaran',
            'cv' => 'CV',
            'ktp_kk' => 'Fotokopi KTP dan KK',
            'ijazah_transkrip' => 'Fotokopi Ijazah dan Transkrip Nilai',
        ];

        $optionalDocs = [
            'skck' => 'Fotokopi SKCK',
            'dokumen_pendukung' => 'Dokumen Pendukung', // Ini bisa jadi multiple
        ];

        // Rules validasi dinamis berdasarkan input yang ada
        $rules = [];
        $messages = [];
        foreach (array_merge($requiredDocs, $optionalDocs) as $field => $name) {
            // Untuk dokumen pendukung yang multiple, atur validasi array
            if ($field === 'dokumen_pendukung') {
                $rules['dokumen_pendukung.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
                $messages['dokumen_pendukung.*.mimes'] = 'File :attribute harus berformat PDF, JPG, JPEG, atau PNG.';
                $messages['dokumen_pendukung.*.max'] = 'Ukuran file :attribute tidak boleh lebih dari 2MB.';
            } else {
                $rules[$field] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
                $messages["$field.mimes"] = 'File :attribute harus berformat PDF, JPG, JPEG, atau PNG.';
                $messages["$field.max"] = 'Ukuran file :attribute tidak boleh lebih dari 2MB.';
            }
        }
        
        // Jalankan validasi
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $uploadedCount = 0; // Untuk menghitung berapa dokumen yang berhasil diunggah

        // Proses setiap jenis dokumen yang diunggah
        foreach ($requiredDocs as $field => $docName) {
            if ($request->hasFile($field)) {
                $this->saveDocument($request->file($field), $application, $docName, $field);
                $uploadedCount++;
            }
        }
        foreach ($optionalDocs as $field => $docName) {
            if ($field === 'dokumen_pendukung' && $request->hasFile($field)) {
                foreach ($request->file($field) as $index => $file) {
                    $this->saveDocument($file, $application, $docName . ' (' . ($index + 1) . ')', $field . '_' . ($index + 1));
                    $uploadedCount++;
                }
            } elseif ($request->hasFile($field)) {
                $this->saveDocument($request->file($field), $application, $docName, $field);
                $uploadedCount++;
            }
        }

        if ($uploadedCount === 0) {
            return back()->with('error', 'Tidak ada dokumen yang diunggah. Pilih setidaknya satu file.');
        }

        // Cek kelengkapan dokumen wajib setelah upload
        $this->checkAndUpdateApplicationStatus($application);

        return back()->with('status', 'Dokumen berhasil diunggah!');
    }

    /**
     * Helper function untuk menyimpan file dan record database.
     * @param \Illuminate\Http\UploadedFile $file
     * @param \App\Models\Application $application
     * @param string $docName
     * @param string $fieldKey Digunakan untuk identifikasi unik dokumen (misal: 'surat_lamaran')
     */
    private function saveDocument($file, Application $application, string $docName, string $fieldKey)
    {
        // Buat nama file unik berdasarkan fieldKey
        $fileName = $fieldKey . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('application_documents/' . $application->id, $fileName, 'public');

        // Gunakan updateOrCreate untuk mengganti dokumen jika sudah ada, atau membuat baru
        // Kita asumsikan document_name + application_id adalah unique constraint untuk dokumen wajib
        // Untuk dokumen pendukung, biarkan saja selalu buat baru jika namanya dinamis.
        $application->documents()->updateOrCreate(
            ['application_id' => $application->id, 'document_name' => $docName],
            ['file_path' => $path]
        );
    }

    /**
     * Helper function untuk mengecek kelengkapan dokumen wajib dan update status aplikasi.
     * @param \App\Models\Application $application
     */
    private function checkAndUpdateApplicationStatus(Application $application)
    {
        $requiredDocsNames = [
            'Surat Lamaran',
            'CV',
            'Fotokopi KTP dan KK',
            'Fotokopi Ijazah dan Transkrip Nilai',
        ];

        // Ambil nama dokumen yang sudah diunggah untuk aplikasi ini
        $uploadedDocNames = $application->documents()->pluck('document_name')->toArray();
        
        // Cek apakah semua dokumen WAJIB sudah diunggah
        $allRequiredUploaded = true;
        foreach ($requiredDocsNames as $requiredName) {
            if (!in_array($requiredName, $uploadedDocNames)) {
                $allRequiredUploaded = false;
                break;
            }
        }

        // Jika semua dokumen wajib sudah ada dan status masih 'belum_lengkap', update ke 'menunggu_seleksi'
        if ($allRequiredUploaded && $application->status === 'belum_lengkap') {
            $application->update(['status' => 'menunggu_seleksi']);
        }
    }
}