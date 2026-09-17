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

    public function index(Request $request)
    {
        $pengguna = PenggunaLulusan::withCount('lulusans')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $term = $request->string('cari')->trim()->toString();
                $query->where(function ($search) use ($term) {
                    $search->where('nama_perusahaan', 'like', "%{$term}%")
                        ->orWhere('nama_penyelia', 'like', "%{$term}%")
                        ->orWhere('email_penyelia', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('jenis'), fn ($query) => $query->where('jenis_perusahaan', $request->jenis))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();
        $jenisList = PenggunaLulusan::whereNotNull('jenis_perusahaan')->distinct()->orderBy('jenis_perusahaan')->pluck('jenis_perusahaan');
        return view('admin.penggunalulusan.index', compact('pengguna', 'jenisList'));
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
