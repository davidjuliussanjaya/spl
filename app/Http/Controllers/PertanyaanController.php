<?php

namespace App\Http\Controllers;

use App\Http\Requests\PertanyaanStoreRequest;
use App\Models\soal;
use App\Services\PertanyaanService;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
   protected $pertanyaanService;

    public function __construct(PertanyaanService $pertanyaanService)
    {
        $this->pertanyaanService = $pertanyaanService;
    }

    public function index()
    {
        $soal = Soal::with('kategori')->latest()->get();
        return view('admin.pertanyaan.index', compact('soal'));
    }

    public function add()
    {
        $kategoris = \App\Models\Kategori::all();
        return view('admin.pertanyaan.add', compact('kategoris'));
    }

    public function store(PertanyaanStoreRequest $request)
    {
        $this->pertanyaanService->storePertanyaan($request->all());

        return redirect()->route('pertanyaan')->with('success', 'Soal berhasil disimpan!');
    }

    public function edit($id)
    {
        $soal = Soal::with('jawaban')->findOrFail($id);
        $kategoris = \App\Models\Kategori::all();
        return view('admin.pertanyaan.edit', compact('soal', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $this->pertanyaanService->updatePertanyaan($id, $request->all());

        return redirect()->route('pertanyaan')->with('success', 'Soal berhasil diupdate!');
    }

    public function switch($id)
    {
        $this->pertanyaanService->toggleStatus($id);
        return redirect()->back()->with('success', 'Status berhasil diperbarui!');
    }
}
