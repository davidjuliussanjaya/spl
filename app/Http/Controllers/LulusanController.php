<?php

namespace App\Http\Controllers;

use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Services\LulusanService;
use Illuminate\Http\Request;

class LulusanController extends Controller
{
    protected $lulusanService;

    public function index(Request $request)
    {
        $query = lulusan::query();

        // Filter Nama
        if ($request->has('nama') && $request->nama != '') {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter NIM
        if ($request->has('nim') && $request->nim != '') {
            $query->where('nim', 'like', '%' . $request->nim . '%');
        }

        // Filter Prodi
        if ($request->has('prodi') && $request->prodi != 'Select') {
            $query->where('program_studi', $request->prodi);
        }

        // Filter Tahun Lulus (Range)
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tahun_lulus', [$request->dari . '-01-01', $request->sampai . '-12-31']);
        }

        // Filter Status (Boolean di DB, tapi di UI Bekerja/Belum)
        if ($request->has('status_kerja') && $request->status_kerja != 'Select') {
            $statusValue = ($request->status_kerja == 'Bekerja') ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $lulusan = $query->latest()->get();

        return view('admin.lulusan.index', compact('lulusan'));
    }
    public function add()
    {
        $perusahaan = \App\Models\PenggunaLulusan::select('id', 'nama_perusahaan')->get();
        
        return view('admin.lulusan.add', compact('perusahaan'));
    }
    public function __construct(LulusanService $lulusanService)
    {
        $this->lulusanService = $lulusanService;
    }

    public function create()
    {
        $perusahaan = penggunalulusan::select('id', 'nama_perusahaan')->get();
        return view('lulusan.create', compact('perusahaan'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'pengguna_lulusan_id' => 'required|exists:pengguna_lulusan,id',
            'nama'                => 'required|string|max:255',
            'nim'                 => 'required|string|unique:lulusan,nim',
            'program_studi'       => 'required|string',
            'tahun_lulus'         => 'required|date',
            'status'              => 'nullable',
        ]);

        try {
            // 2. Eksekusi Logika melalui Service
            $this->lulusanService->storeLulusan($validated);

            // 3. Redirect dengan feedback
            return redirect()->route('lulusan') // Sesuaikan route index Anda
                ->with('success', 'Data lulusan berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
