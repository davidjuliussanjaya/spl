<?php

namespace App\Http\Controllers;

use App\Models\penggunalulusan;
use App\Models\ResponJawaban;
use App\Models\soal;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    // Pastikan model Survey sudah di-import di atas: use App\Models\Survey;

public function index()
{
    // Mengambil semua data survey, diurutkan dari yang terbaru (latest)
    // with() digunakan untuk memuat relasi (Eager Loading) agar tidak terjadi N+1 problem dan query lebih cepat
    $surveys = Survey::with(['lulusan', 'penggunalulusan'])->latest()->get();

    // Mengirim variabel $surveys ke halaman view
    return view('admin.survey.index', compact('surveys'));
}

    public function getPerusahaanData($id)
    {
        $data = \App\Models\PenggunaLulusan::find($id);
        return response()->json($data);
    }
    public function add()
{
    $perusahaan = \App\Models\PenggunaLulusan::all();
    
    // Ambil semua soal aktif untuk dipilih oleh Admin
    $daftarSoal = \App\Models\Soal::with('jawaban')
        ->where('is_active', 1)
        ->get();

    // Tambahkan data lulusan jika diperlukan di form
    $lulusan = \App\Models\Lulusan::all(); 

    return view('admin.survey.add', compact('perusahaan', 'daftarSoal', 'lulusan'));
}

    public function store(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'judul'               => 'required|string|max:255',
        'lulusan_id'          => 'required|exists:lulusan,id',
        'pengguna_lulusan_id' => 'required|exists:pengguna_lulusan,id',
        'soal_pilihan'        => 'required|array|min:1', 
        // Validasi opsional untuk field yang diisi manual
        'nama'                => 'nullable|string|max:255',
        'hp'                  => 'nullable|string|max:50',
        'email'               => 'nullable|email|max:255',
        'badan_hukum'         => 'nullable|string|max:255',
        'telp_perusahaan'     => 'nullable|string|max:50',
        'alamat_perusahaan'   => 'nullable|string',
    ]);

    try {
        DB::beginTransaction();

        // 2. Cek & Update Data Pengguna Lulusan (Perusahaan & Penyelia)
        $pengguna = penggunalulusan::findOrFail($request->pengguna_lulusan_id);
        
        // Kita update datanya dengan inputan form. 
        // Jika inputan form kosong (dihapus admin), kita kembalikan ke data aslinya menggunakan ??
        $pengguna->update([
            'nama_penyelia'     => $request->nama ?? $pengguna->nama_penyelia,
            'kontak_penyelia'   => $request->hp ?? $pengguna->kontak_penyelia,
            'email_penyelia'    => $request->email ?? $pengguna->email_penyelia,
            'nomor_badan_hukum' => $request->badan_hukum ?? $pengguna->nomor_badan_hukum,
            'kontak_perusahaan' => $request->telp_perusahaan ?? $pengguna->kontak_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan ?? $pengguna->alamat_perusahaan,
        ]);

        // 3. Simpan Header Survey
        $survey = Survey::create([
            'judul'               => $request->judul,
            'deskripsi'           => $request->deskripsi, // Disimpan jika ada
            'lulusan_id'          => $request->lulusan_id,
            'pengguna_lulusan_id' => $request->pengguna_lulusan_id,
            'access_code'         => strtoupper(Str::random(8)),
            'is_completed'        => false,
            'is_active'           => true,
        ]);

        // 4. Simpan Soal yang dipilih ke tabel pivot survey_soal
        // Jika Anda sudah menambahkan method soals() di model Survey, Anda bisa pakai:
        // $survey->soals()->attach($request->soal_pilihan);
        
        // Tapi jika pakai DB query builder sesuai kode Anda:
        foreach ($request->soal_pilihan as $soal_id) {
            DB::table('survey_soal')->insert([
                'survey_id'  => $survey->id,
                'soal_id'    => $soal_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();
        return redirect()->route('survey')->with('success', 'Sesi Survey berhasil dibuat dan data instansi tersinkronisasi.');
        
    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
    }
}
    public function verifyCode(Request $request)
{
    $survey = Survey::where('access_code', $request->code)
                    ->where('is_completed', false)
                    ->first();

    if (!$survey) {
        return back()->with('error', 'Kode akses tidak valid atau survey telah selesai.');
    }

    return redirect()->route('survey.fill', $survey->access_code);
}

public function fill($code)
{
    $survey = Survey::with(['lulusan', 'penggunalulusan'])
                    ->where('access_code', $code)
                    ->firstOrFail();

    // Ambil soal hanya yang dipilih admin (lewat tabel pivot survey_soal)
    $soal = Soal::whereHas('surveys', function($q) use ($survey) {
        $q->where('survey_id', $survey->id);
    })->with('jawaban')->get();

    return view('fill_page', compact('survey', 'soal'));
}
public function submitJawaban(Request $request, $code)
{
    $survey = Survey::where('access_code', $code)->firstOrFail();

    try {
        DB::beginTransaction();

        // Loop setiap jawaban yang dikirim
        foreach ($request->jawaban as $soal_id => $isi_jawaban) {
            $soal = Soal::find($soal_id);

            $respon = new ResponJawaban();
            $respon->survey_id = $survey->id;
            $respon->soal_id = $soal_id;
            $respon->responden = $request->nama_pengisi; // Nama orang yang mengisi form

            // Logika pemisahan Essay dan PG
            if ($soal->jenis_soal == 'essay') {
                $respon->jawaban_text = $isi_jawaban;
                $respon->jawaban_id = null;
            } else {
                $respon->jawaban_id = $isi_jawaban; // ID dari tabel jawaban
                $respon->jawaban_text = null;
            }

            $respon->save();
        }

        // Kunci survey agar kode tidak bisa digunakan lagi
        $survey->update(['is_completed' => true]);

        DB::commit();
        return redirect('/')->with('success', 'Terima kasih, survey berhasil terkirim!');

    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}
}
