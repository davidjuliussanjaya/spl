<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenggunaLulusanController extends Controller
{
    public function index()
    {
        // Mengambil semua data pengguna lulusan
        $pengguna = \App\Models\PenggunaLulusan::latest()->get();
        return view('admin.penggunalulusan.index', compact('pengguna'));
    }
    public function create()
    {
        return view('admin.penggunalulusan.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_penyelia' => 'required|string|max:255',
            'email_penyelia' => 'required|email|unique:pengguna_lulusan,email_penyelia',
            'kontak_penyelia' => 'nullable|string',
            'jenis_perusahaan' => 'required|in:government,private,startup,nonprofit',
            'alamat_perusahaan' => 'nullable|string',
            // tambahkan validasi lainnya sesuai kebutuhan
        ]);

        // Konversi checkbox ke boolean
        $validated['cabang_kota'] = $request->has('cabang_kota');
        $validated['cabang_negara'] = $request->has('cabang_negara');

        \App\Models\PenggunaLulusan::create($validated);

        return redirect()->route('penggunalulusan')->with('success', 'Instansi berhasil didaftarkan');
    }
}
