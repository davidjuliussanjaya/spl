<?php

namespace App\Http\Controllers;

use App\Http\Requests\LulusanStoreRequest;
use App\Models\lulusan;
use App\Models\penggunalulusan;
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

        return view('admin.lulusan.index', compact('lulusan'));
    }

    public function add()
    {
        $perusahaan = \App\Models\PenggunaLulusan::select('id', 'nama_perusahaan')->get();
        
        return view('admin.lulusan.add', compact('perusahaan'));
    }

    public function create()
    {
        $perusahaan = penggunalulusan::select('id', 'nama_perusahaan')->get();
        return view('lulusan.create', compact('perusahaan'));
    }

    public function show($id)
    {
        $lulusan = \App\Models\lulusan::with('pengguna')->findOrFail($id);

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
}
