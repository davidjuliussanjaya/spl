<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Fakultas;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = Kategori::with('fakultas')->latest('created_at')->paginate(10);
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        $fakultasList = Fakultas::orderBy('kode')->get();

        return view('admin.kategori.create', compact('fakultasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:utama,optional',
            'fakultas_id' => 'nullable|exists:fakultas,id',
        ]);

        Kategori::create($request->only(['nama_kategori', 'deskripsi', 'status', 'fakultas_id']));

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Kategori $kategori)
    {
        return view('admin.kategori.show', compact('kategori'));
    }

    public function edit(Kategori $kategori)
    {
        $fakultasList = Fakultas::orderBy('kode')->get();

        return view('admin.kategori.edit', compact('kategori', 'fakultasList'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:utama,optional',
            'fakultas_id' => 'nullable|exists:fakultas,id',
        ]);

        $kategori->update($request->only(['nama_kategori', 'deskripsi', 'status', 'fakultas_id']));

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
