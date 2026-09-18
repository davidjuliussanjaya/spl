<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::withCount('surveys')
            ->orderByDesc('tanggal_mulai')
            ->paginate(10);

        return view('admin.periode.index', compact('periodes'));
    }

    public function create()
    {
        return view('admin.periode.create');
    }

    public function store(Request $request)
    {
        Periode::create($this->validatedData($request));

        return redirect()->route('periode.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function edit(Periode $periode)
    {
        return view('admin.periode.edit', compact('periode'));
    }

    public function update(Request $request, Periode $periode)
    {
        $periode->update($this->validatedData($request, $periode));

        return redirect()->route('periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode)
    {
        if ($periode->surveys()->exists()) {
            return back()->with('error', 'Periode tidak dapat dihapus karena sudah digunakan oleh survei.');
        }

        $periode->delete();

        return redirect()->route('periode.index')->with('success', 'Periode berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Periode $periode = null): array
    {
        return $request->validate([
            'kode_periode' => [
                'required', 'string', 'max:50',
                Rule::unique('periode', 'kode_periode')->ignore($periode?->id),
            ],
            'nama_periode' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date', 'before_or_equal:tanggal_berakhir'],
            'tanggal_berakhir' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);
    }
}
