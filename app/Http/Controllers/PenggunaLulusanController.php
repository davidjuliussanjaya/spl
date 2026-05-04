<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenggunaLulusanStoreRequest;
use App\Http\Requests\PenggunaLulusanUpdateRequest;
use App\Models\PenggunaLulusan;
use App\Services\PenggunaLulusanService;
use Illuminate\Http\Request;

class PenggunaLulusanController extends Controller
{
    protected $penggunaService;

    public function __construct(PenggunaLulusanService $penggunaService)
    {
        $this->penggunaService = $penggunaService;
    }

    public function index()
    {
        // Mengambil semua data pengguna lulusan
        $pengguna = PenggunaLulusan::latest()->get();
        return view('admin.penggunalulusan.index', compact('pengguna'));
    }

    public function create()
    {
        return view('admin.penggunalulusan.add');
    }

    public function store(PenggunaLulusanStoreRequest $request)
    {
        $this->penggunaService->storePengguna($request->validated(), $request);

        return redirect()->route('penggunalulusan')->with('success', 'Instansi berhasil didaftarkan');
    }

    public function edit($id)
    {
        $pengguna = PenggunaLulusan::findOrFail($id);
        return view('admin.penggunalulusan.edit', compact('pengguna'));
    }

    public function update(PenggunaLulusanUpdateRequest $request, $id)
    {
        $this->penggunaService->updatePengguna($id, $request->validated(), $request);

        return redirect()->route('penggunalulusan')->with('success', 'Instansi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $this->penggunaService->deletePengguna($id);

        return redirect()->route('penggunalulusan')->with('success', 'Instansi berhasil dihapus');
    }
}

