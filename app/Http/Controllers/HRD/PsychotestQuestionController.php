<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\PsychotestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PsychotestQuestionController extends Controller
{
    public function index()
    {
        $questions = PsychotestQuestion::with('options')->latest()->paginate();
        return view('hrd.psychotest_questions.index', compact('questions'));
    }

    public function create()
    {
        return view('hrd.psychotest_questions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:4',
            'options.*' => 'required|string',
            'is_correct' => 'required|in:0,1,2,3',
        ]);

        DB::transaction(function () use ($validated) {
            $question = PsychotestQuestion::create(['question_text' => $validated['question_text']]);

            foreach ($validated['options'] as $index => $optionText) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => ($index == $validated['is_correct'])
                ]);
            }
        });

        return redirect()->route('hrd.psychotest_questions.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(PsychotestQuestion $soal_psikote)
    {
        $soal_psikote->load('options');
        return view('hrd.psychotest_questions.edit', ['question' => $soal_psikote]);
    }

    public function update(Request $request, PsychotestQuestion $soal_psikote)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:4',
            'options.*' => 'required|string',
            'is_correct' => 'required|in:0,1,2,3',
        ]);

        DB::transaction(function () use ($validated, $soal_psikote) {
            $soal_psikote->update(['question_text' => $validated['question_text']]);
            // Hapus opsi lama dan buat yang baru untuk simplicity
            $soal_psikote->options()->delete();
            foreach ($validated['options'] as $index => $optionText) {
                $soal_psikote->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => ($index == $validated['is_correct'])
                ]);
            }
        });

        return redirect()->route('hrd.psychotest_questions.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(PsychotestQuestion $soal_psikote)
    {
        $soal_psikote->delete();
        return redirect()->route('hrd.psychotest_questions.index')->with('success', 'Soal berhasil dihapus.');
    }
}