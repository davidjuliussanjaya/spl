<?php

use App\Models\Lulusan;
use App\Models\PenggunaLulusan;
use App\Models\Kategori;
use App\Models\Jawaban;
use App\Models\Role;
use App\Models\Soal;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Periode;
use App\Models\Survey;
use App\Models\User;

beforeEach(function () {
    $role = Role::create(['code' => 'admin', 'name' => 'Administrator']);
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->roles()->attach($role, ['is_active' => true, 'assigned_at' => now()]);
    $this->fakultas = Fakultas::create(['kode' => 'FTI', 'nama' => 'Fakultas Teknologi dan Informatika']);
    $this->programStudi = ProgramStudi::create(['fakultas_id' => $this->fakultas->id, 'kode' => 'TI', 'nama' => 'Teknik Informatika']);
    $this->periode = Periode::create([
        'kode_periode' => 'TEST-AKTIF',
        'nama_periode' => 'Periode Pengujian Aktif',
        'tanggal_mulai' => now()->subDay(),
        'tanggal_berakhir' => now()->addDay(),
    ]);
});

function perusahaanData(array $overrides = []): array
{
    return array_merge([
        'nama_perusahaan' => 'PT Contoh Nusantara',
        'nama_penyelia' => 'Rina Penyelia',
        'jabatan_penyelia' => 'HR Manager',
        'email_penyelia' => 'rina@example.test',
        'kontak_penyelia' => '08123456789',
        'nomor_badan_hukum' => 'NIB-123456',
        'jenis_perusahaan' => 'Teknologi Informasi / Software / Digital',
        'alamat_perusahaan' => 'Jl. Contoh No. 1',
        'cabang_kota' => 2,
        'cabang_negara' => 0,
        'durasi_lulusan_bekerja' => 12,
    ], $overrides);
}

test('administrator can open the company list and add form', function () {
    $this->actingAs($this->admin)
        ->get(route('penggunalulusan'))
        ->assertOk();

    $this->actingAs($this->admin)
        ->get(route('create'))
        ->assertOk();
});

test('administrator can add, edit, and delete a company', function () {
    $this->actingAs($this->admin)
        ->post(route('pengguna.store'), perusahaanData())
        ->assertRedirect(route('penggunalulusan'));

    $perusahaan = PenggunaLulusan::where('email_penyelia', 'rina@example.test')->firstOrFail();

    $this->actingAs($this->admin)
        ->get(route('penggunalulusan.edit', $perusahaan->id))
        ->assertOk();

    $this->actingAs($this->admin)
        ->put(route('penggunalulusan.update', $perusahaan->id), perusahaanData([
            'nama_perusahaan' => 'PT Contoh Diperbarui',
        ]))
        ->assertRedirect(route('penggunalulusan'));

    $this->assertDatabaseHas('pengguna_lulusan', [
        'id' => $perusahaan->id,
        'nama_perusahaan' => 'PT Contoh Diperbarui',
    ]);

    $this->actingAs($this->admin)
        ->delete(route('penggunalulusan.destroy', $perusahaan->id))
        ->assertRedirect(route('penggunalulusan'));

    $this->assertDatabaseMissing('pengguna_lulusan', ['id' => $perusahaan->id]);
});

test('administrator can open, add, and view a graduate', function () {
    $perusahaan = PenggunaLulusan::create(perusahaanData());

    $this->actingAs($this->admin)
        ->get(route('lulusan'))
        ->assertOk();

    $this->actingAs($this->admin)
        ->get(route('addgrad'))
        ->assertOk();

    $this->actingAs($this->admin)
        ->post(route('lulusan.store'), [
            'pengguna_lulusan_id' => $perusahaan->id,
            'nama' => 'Budi Lulusan',
            'nim' => '22410100009',
            'program_studi_id' => $this->programStudi->id,
            'fakultas_id' => $this->fakultas->id,
            'tahun_lulus' => '2025-08-01',
            'status' => '1',
        ])
        ->assertRedirect(route('lulusan'));

    $lulusan = Lulusan::where('nim', '22410100009')->firstOrFail();

    $this->actingAs($this->admin)
        ->get(route('lulusan.show', $lulusan->id))
        ->assertOk();
});

test('administrator can open every remaining management page', function () {
    $kategori = Kategori::create([
        'nama_kategori' => 'Kompetensi',
        'deskripsi' => 'Penilaian kompetensi lulusan',
    ]);

    Soal::create([
        'soal' => 'Kemampuan teknis lulusan',
        'kode' => 'A1',
        'kategori_id' => $kategori->id,
        'jenis_soal' => 'rating',
        'is_required' => true,
        'is_active' => true,
    ]);

    foreach ([
        'dashboard',
        'survey',
        'addsurvey',
        'survey.bulk',
        'pertanyaan',
        'addquestion',
        'kategori.index',
        'kategori.create',
        'report',
        'report.arsip',
    ] as $route) {
        $this->actingAs($this->admin)->get(route($route))->assertOk();
    }

    $this->actingAs($this->admin)
        ->get(route('kategori.edit', $kategori))
        ->assertOk();
});

test('administrator can create, update, and delete a survey', function () {
    $perusahaan = PenggunaLulusan::create(perusahaanData());
    $lulusan = Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Budi Lulusan',
        'nim' => '22410100009',
        'program_studi' => 'Teknik Informatika',
        'fakultas' => 'FTI',
        'program_studi_id' => $this->programStudi->id,
        'fakultas_id' => $this->fakultas->id,
        'tahun_lulus' => '2025-08-01',
        'status' => true,
    ]);
    $soal = Soal::create([
        'soal' => 'Kemampuan teknis lulusan',
        'kode' => 'A1',
        'jenis_soal' => 'rating',
        'is_required' => true,
        'is_active' => true,
    ]);
    $data = [
        'judul' => 'Survey Pengguna Lulusan 2026',
        'periode_id' => $this->periode->id,
        'lulusan_id' => $lulusan->id,
        'pengguna_lulusan_id' => $perusahaan->id,
        'soal_pilihan' => [$soal->id],
    ];

    $this->actingAs($this->admin)
        ->post(route('survey.store'), $data)
        ->assertRedirect(route('survey', ['periode_id' => $this->periode->id]));

    $survey = Survey::firstOrFail();

    $this->actingAs($this->admin)
        ->get(route('survey.edit', $survey->id))
        ->assertOk();

    $this->actingAs($this->admin)
        ->put(route('survey.update', $survey->id), array_merge($data, [
            'judul' => 'Survey Pengguna Lulusan Diperbarui',
        ]))
        ->assertRedirect(route('survey', ['periode_id' => $this->periode->id]));

    $this->actingAs($this->admin)
        ->delete(route('survey.destroy', $survey->id))
        ->assertRedirect(route('survey', ['periode_id' => $this->periode->id]));
});

test('administrator can create and update a question without entering an optional code', function () {
    $kategori = Kategori::create(['nama_kategori' => 'Kompetensi']);
    $data = [
        'question' => 'Bagaimana kemampuan teknis lulusan?',
        'kategori_id' => $kategori->id,
        'type' => 'text',
        'required' => '1',
    ];

    $this->actingAs($this->admin)
        ->post(route('savequestion'), $data)
        ->assertRedirect(route('pertanyaan'));

    $soal = Soal::firstOrFail();
    expect($soal->kode)->toStartWith('Q-');

    $this->actingAs($this->admin)
        ->get(route('pertanyaan.edit', $soal->id))
        ->assertOk();

    $this->actingAs($this->admin)
        ->put(route('pertanyaan.update', $soal->id), array_merge($data, [
            'question' => 'Bagaimana kemampuan teknis lulusan setelah diperbarui?',
        ]))
        ->assertRedirect(route('pertanyaan'));

    $this->actingAs($this->admin)
        ->patch(route('pertanyaan.switch', $soal->id))
        ->assertRedirect();

    $this->assertDatabaseHas('soal', ['id' => $soal->id, 'is_active' => false]);
});

test('public survey access, fill, and submit flow works', function () {
    $perusahaan = PenggunaLulusan::create(perusahaanData());
    $lulusan = Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Budi Lulusan',
        'nim' => '22410100009',
        'program_studi' => 'Teknik Informatika',
        'fakultas' => 'FTI',
        'program_studi_id' => $this->programStudi->id,
        'fakultas_id' => $this->fakultas->id,
        'tahun_lulus' => '2025-08-01',
        'status' => true,
    ]);
    $soal = Soal::create([
        'soal' => 'Kemampuan teknis lulusan',
        'kode' => 'A1',
        'jenis_soal' => 'rating',
        'is_required' => true,
        'is_active' => true,
    ]);
    $jawaban = Jawaban::create([
        'soal_id' => $soal->id,
        'jawaban' => 'Sangat Baik',
        'nilai' => 4,
        'urutan' => 1,
    ]);
    $survey = Survey::create([
        'judul' => 'Survey Kepuasan',
        'periode_id' => $this->periode->id,
        'lulusan_id' => $lulusan->id,
        'pengguna_lulusan_id' => $perusahaan->id,
        'access_code' => 'TEST2026',
        'is_completed' => false,
        'is_active' => true,
    ]);
    $survey->soals()->attach($soal);

    $this->post(route('survey.access'), ['code' => $survey->access_code])
        ->assertRedirect(route('survey.fill', $survey->access_code));

    $this->get(route('survey.fill', $survey->access_code))->assertOk();

    $this->from(route('survey.fill', $survey->access_code))
        ->post(route('survey.submit', $survey->access_code), [
            'nama_pengisi' => 'Rina Penyelia',
            'nama_perusahaan' => $perusahaan->nama_perusahaan,
            'jumlah_lulusan_bekerja' => 0,
        ])
        ->assertRedirect(route('survey.fill', $survey->access_code))
        ->assertSessionHasErrors(['jumlah_lulusan_bekerja', 'jawaban.' . $soal->id]);

    $this->post(route('survey.submit', $survey->access_code), [
        'nama_pengisi' => 'Rina Penyelia',
        'nama_perusahaan' => $perusahaan->nama_perusahaan,
        'jumlah_lulusan_bekerja' => 1,
        'jawaban' => [$soal->id => $jawaban->id],
    ])->assertRedirect('/');

    $this->assertDatabaseHas('survey', ['id' => $survey->id, 'is_completed' => true]);
    $this->assertDatabaseHas('respon_jawaban', ['survey_id' => $survey->id, 'jawaban_id' => $jawaban->id]);
});

test('administrator can manage categories, use the company data endpoint, and download a report', function () {
    $this->actingAs($this->admin)
        ->post(route('kategori.store'), [
            'nama_kategori' => 'Kompetensi',
            'deskripsi' => 'Penilaian kompetensi lulusan',
            'status' => 'utama',
        ])
        ->assertRedirect(route('kategori.index'));

    $kategori = Kategori::firstOrFail();

    $this->actingAs($this->admin)
        ->put(route('kategori.update', $kategori), [
            'nama_kategori' => 'Kompetensi Diperbarui',
            'deskripsi' => 'Deskripsi diperbarui',
            'status' => 'optional',
        ])
        ->assertRedirect(route('kategori.index'));

    $perusahaan = PenggunaLulusan::create(perusahaanData());
    $this->actingAs($this->admin)
        ->get('/get-perusahaan/' . $perusahaan->id)
        ->assertOk()
        ->assertJsonPath('id', $perusahaan->id);

    $this->actingAs($this->admin)
        ->get(route('report.download'))
        ->assertOk();

    $this->actingAs($this->admin)
        ->delete(route('kategori.destroy', $kategori))
        ->assertRedirect(route('kategori.index'));
});

test('administrator can generate surveys in bulk', function () {
    $perusahaan = PenggunaLulusan::create(perusahaanData());
    Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Budi Lulusan',
        'nim' => '22410100009',
        'program_studi' => 'Teknik Informatika',
        'fakultas' => 'FTI',
        'program_studi_id' => $this->programStudi->id,
        'fakultas_id' => $this->fakultas->id,
        'tahun_lulus' => '2025-08-01',
        'status' => true,
    ]);
    $soal = Soal::create([
        'soal' => 'Kemampuan teknis lulusan',
        'kode' => 'A1',
        'jenis_soal' => 'rating',
        'is_required' => true,
        'is_active' => true,
    ]);

    $this->actingAs($this->admin)
        ->post(route('survey.bulk.store'), [
            'judul' => 'Survey Massal 2026',
            'periode_id' => $this->periode->id,
            'tahun_lulus' => 2025,
            'soal_pilihan' => [$soal->id],
        ])
        ->assertRedirect(route('survey', ['periode_id' => $this->periode->id]));

    $this->assertDatabaseCount('survey', 1);
});
