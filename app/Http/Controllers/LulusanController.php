<?php

namespace App\Http\Controllers;

use App\Http\Requests\LulusanStoreRequest;
use App\Models\Lulusan;
use App\Models\PenggunaLulusan;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Services\LulusanService;
use Illuminate\Http\Request;

class LulusanController extends Controller
{
    protected $lulusanService;

    public function __construct(LulusanService $lulusanService)
    {
        $this->lulusanService = $lulusanService;
    }

    public function index(Request $request)
    {
        $lulusan = $this->lulusanService->getFilteredLulusan($request);
        $fakultasList = Fakultas::with('programStudis')->orderBy('kode')->get();
        $prodiList = ProgramStudi::with('fakultas')->orderBy('nama')->get();

        return view('admin.lulusan.index', compact('lulusan', 'fakultasList', 'prodiList'));
    }

    public function add()
    {
        $perusahaan = \App\Models\PenggunaLulusan::select('id', 'nama_perusahaan')->get();
        $fakultasList = Fakultas::with('programStudis')->orderBy('kode')->get();
        
        return view('admin.lulusan.add', compact('perusahaan', 'fakultasList'));
    }

    public function create()
    {
        $perusahaan = PenggunaLulusan::select('id', 'nama_perusahaan')->get();
        $fakultasList = Fakultas::with('programStudis')->orderBy('kode')->get();
        return view('lulusan.create', compact('perusahaan', 'fakultasList'));
    }

    public function show($id)
    {
        $lulusan = Lulusan::with(['pengguna', 'fakultasMaster', 'programStudi'])->findOrFail($id);

        return view('admin.lulusan.show', compact('lulusan'));
    }

    public function store(LulusanStoreRequest $request)
    {
        try {
            // Eksekusi Logika melalui Service
            $this->lulusanService->storeLulusan($request->validated());

            // Redirect dengan feedback
            return redirect()->route('lulusan') // Sesuaikan route index Anda
                ->with('success', 'Data lulusan berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Titik integrasi untuk sinkronisasi data mahasiswa dari REST API eksternal.
     *
     * Konfigurasi endpoint, autentikasi, dan pemetaan data akan ditambahkan
     * setelah detail API dari sumber data tersedia.
     */
    public function syncMahasiswa()
    {
        return redirect()->route('lulusan')->with(
            'error',
            'Sinkronisasi data mahasiswa belum dapat dijalankan karena konfigurasi REST API belum tersedia.'
        );
    }
}
