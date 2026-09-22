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
use App\Models\SurveyArsip;
use App\Models\User;
use App\Services\DashboardService;

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

test('administrator can manage survey periods', function () {
    $this->actingAs($this->admin)
        ->get(route('periode.index'))
        ->assertOk();

    $this->actingAs($this->admin)
        ->get(route('periode.create'))
        ->assertOk();

    $this->actingAs($this->admin)
        ->post(route('periode.store'), [
            'kode_periode' => 'TEST-BARU',
            'nama_periode' => 'Periode Pengujian Baru',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_berakhir' => '2026-12-31',
        ])
        ->assertRedirect(route('periode.index'));

    $periode = Periode::where('kode_periode', 'TEST-BARU')->firstOrFail();

    $this->actingAs($this->admin)
        ->get(route('periode.edit', $periode))
        ->assertOk();

    $this->actingAs($this->admin)
        ->put(route('periode.update', $periode), [
            'kode_periode' => 'TEST-DIPERBARUI',
            'nama_periode' => 'Periode Pengujian Diperbarui',
            'tanggal_mulai' => '2026-02-01',
            'tanggal_berakhir' => '2026-12-31',
        ])
        ->assertRedirect(route('periode.index'));

    $this->actingAs($this->admin)
        ->delete(route('periode.destroy', $periode))
        ->assertRedirect(route('periode.index'));

    $this->assertDatabaseMissing('periode', ['id' => $periode->id]);
});

test('non-administrators cannot access administration features', function () {
    $userRole = Role::firstOrCreate(['code' => 'user'], ['name' => 'User']);
    $regularUser = User::factory()->create(['is_active' => true]);
    $regularUser->roles()->attach($userRole, ['is_active' => true, 'assigned_at' => now()]);

    foreach ([
        'users.index',
        'lulusan',
        'penggunalulusan',
        'periode.index',
        'survey',
        'pertanyaan',
        'kategori.index',
        'report',
    ] as $route) {
        $this->actingAs($regularUser)->get(route($route))->assertForbidden();
    }
});

test('administrator can compare category scores across survey periods', function () {
    foreach ([
        ['kode' => 'TS-2024', 'nama' => 'Tracer Study 2024', 'tanggal' => '2024-01-01', 'nilai' => 3],
        ['kode' => 'TS-2025', 'nama' => 'Tracer Study 2025', 'tanggal' => '2025-01-01', 'nilai' => 4],
    ] as $archive) {
        SurveyArsip::create([
            'periode_kode' => $archive['kode'],
            'periode_nama' => $archive['nama'],
            'periode_tanggal_mulai' => $archive['tanggal'],
            'submitted_at' => $archive['tanggal'],
            'lulusan_program_studi' => $this->programStudi->nama,
            'jawaban_json' => [[
                'kategori' => 'Kompetensi Teknis',
                'jenis' => 'rating',
                'nilai' => $archive['nilai'],
            ]],
        ]);
    }

    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Perbandingan Nilai Kategori Antarperiode')
        ->assertSee('Kompetensi Teknis')
        ->assertSee('Tracer Study 2024')
        ->assertSee('Tracer Study 2025');
});

test('dashboard applies active filters to every data set used by its cards, charts, and tables', function () {
    $periodeTerpilih = Periode::create([
        'kode_periode' => 'FILTER-2025',
        'nama_periode' => 'Periode Terpilih',
        'tanggal_mulai' => '2025-01-01',
        'tanggal_berakhir' => '2025-12-31',
    ]);
    $periodeLain = Periode::create([
        'kode_periode' => 'FILTER-2024',
        'nama_periode' => 'Periode Lain',
        'tanggal_mulai' => '2024-01-01',
        'tanggal_berakhir' => '2024-12-31',
    ]);
    $prodiLain = ProgramStudi::create([
        'fakultas_id' => $this->fakultas->id,
        'kode' => 'SI',
        'nama' => 'Sistem Informasi',
    ]);
    $perusahaan = PenggunaLulusan::create(perusahaanData());

    $lulusanTerpilih = Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Lulusan Terpilih',
        'nim' => '22410100021',
        'program_studi' => $this->programStudi->nama,
        'fakultas' => $this->fakultas->kode,
        'program_studi_id' => $this->programStudi->id,
        'fakultas_id' => $this->fakultas->id,
        'tahun_lulus' => '2025-08-01',
        'status' => true,
    ]);
    $lulusanLain = Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Lulusan Lain',
        'nim' => '22410100022',
        'program_studi' => $prodiLain->nama,
        'fakultas' => $this->fakultas->kode,
        'program_studi_id' => $prodiLain->id,
        'fakultas_id' => $this->fakultas->id,
        'tahun_lulus' => '2024-08-01',
        'status' => true,
    ]);
    $surveyTerpilih = Survey::create([
        'judul' => 'Survei Terpilih', 'periode_id' => $periodeTerpilih->id,
        'lulusan_id' => $lulusanTerpilih->id, 'pengguna_lulusan_id' => $perusahaan->id,
        'access_code' => 'FILTER25', 'is_completed' => true, 'is_active' => true,
    ]);
    $surveyLain = Survey::create([
        'judul' => 'Survei Lain', 'periode_id' => $periodeLain->id,
        'lulusan_id' => $lulusanLain->id, 'pengguna_lulusan_id' => $perusahaan->id,
        'access_code' => 'FILTER24', 'is_completed' => true, 'is_active' => true,
    ]);

    foreach ([
        [$surveyTerpilih, $periodeTerpilih, $this->programStudi->nama, 'Kategori Terpilih', 'Feedback terpilih', 4],
        [$surveyLain, $periodeLain, $prodiLain->nama, 'Kategori Di Luar Filter', 'Feedback di luar filter', 1],
    ] as [$survey, $periode, $prodi, $kategori, $feedback, $nilai]) {
        SurveyArsip::create([
            'survey_id' => $survey->id,
            'pengguna_lulusan_id' => $perusahaan->id,
            'periode_kode' => $periode->kode_periode,
            'periode_nama' => $periode->nama_periode,
            'periode_tanggal_mulai' => $periode->tanggal_mulai,
            'submitted_at' => $periode->tanggal_mulai,
            'lulusan_program_studi' => $prodi,
            'perusahaan_nama' => $perusahaan->nama_perusahaan,
            'jawaban_json' => [
                ['kategori' => $kategori, 'jenis' => 'rating', 'nilai' => $nilai],
                ['kategori' => $kategori, 'soal' => 'Saran', 'jenis' => 'essay', 'jawaban' => $feedback],
            ],
        ]);
    }

    $dashboard = app(DashboardService::class)->getDashboardData([
        'periode' => [$periodeTerpilih->kode_periode],
        'program_studi' => [$this->programStudi->nama],
    ]);

    expect($dashboard['totalSurvey'])->toBe(1)
        ->and($dashboard['totalResponden'])->toBe(1)
        ->and($dashboard['totalLulusan'])->toBe(1)
        ->and($dashboard['chartLabels'])->toBe(['Kategori Terpilih'])
        ->and($dashboard['respondenProdiLabels'])->toBe([$this->programStudi->nama])
        ->and($dashboard['prodiDetails'])->toHaveCount(1)
        ->and($dashboard['kategoriDetails'])->toHaveCount(1)
        ->and($dashboard['periodTrendLabels'])->toBe([$periodeTerpilih->nama_periode])
        ->and($dashboard['categoryComparisonPeriods'])->toBe([
            ['periode' => $periodeTerpilih->kode_periode, 'label' => $periodeTerpilih->nama_periode],
        ])
        ->and($dashboard['komentarTerbaru'])->toHaveCount(1)
        ->and($dashboard['komentarTerbaru']->first()->jawaban_text)->toBe('Feedback terpilih');
});

test('regular users see anonymous feedback and no respondent or graduate totals', function () {
    $userRole = Role::firstOrCreate(['code' => 'user'], ['name' => 'User']);
    $regularUser = User::factory()->create(['is_active' => true]);
    $regularUser->roles()->attach($userRole, ['is_active' => true, 'assigned_at' => now()]);

    SurveyArsip::create([
        'periode_kode' => 'TS-2025',
        'periode_nama' => 'Tracer Study 2025',
        'periode_tanggal_mulai' => '2025-01-01',
        'submitted_at' => '2025-01-01',
        'perusahaan_nama' => 'PT Rahasia Sentosa',
        'penyelia_nama' => 'Nama Responden Rahasia',
        'jawaban_json' => [[
            'kategori' => 'Kompetensi Teknis',
            'jenis' => 'essay',
            'jawaban' => 'Umpan balik anonim.',
        ]],
    ]);

    $this->actingAs($regularUser)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Responden anonim')
        ->assertDontSee('PT Rahasia Sentosa')
        ->assertDontSee('Nama Responden Rahasia')
        ->assertDontSee('Total Responden / NL')
        ->assertDontSee('Total Lulusan / NJ');
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

    $this->actingAs($this->admin)
        ->post(route('survey.store'), $data)
        ->assertRedirect()
        ->assertSessionHas('error', 'Terjadi kesalahan: Lulusan ini sudah memiliki survei pada periode yang dipilih.');

    $this->assertDatabaseCount('survey', 1);

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
        ->post(route('savequestion'), [
            'question' => 'Bagaimana kemampuan komunikasi lulusan?',
            'kategori_id' => $kategori->id,
            'type' => 'rating',
            'required' => '1',
            'jawaban' => ['Sangat Baik', 'Baik'],
            'nilai' => [4, 3],
        ])
        ->assertRedirect(route('pertanyaan'));

    $ratingSoal = Soal::where('soal', 'Bagaimana kemampuan komunikasi lulusan?')->firstOrFail();
    $this->assertDatabaseHas('soal', ['id' => $ratingSoal->id, 'jenis_soal' => 'rating']);
    $this->assertDatabaseHas('jawaban', ['soal_id' => $ratingSoal->id, 'jawaban' => 'Sangat Baik', 'nilai' => 4]);

    $this->actingAs($this->admin)
        ->post(route('savequestion'), [
            'question' => 'Apakah lulusan direkomendasikan?',
            'kategori_id' => $kategori->id,
            'type' => 'radio',
            'jawaban' => ['Ya', 'Tidak'],
            'nilai' => [1, 0],
        ])
        ->assertRedirect(route('pertanyaan'));

    $pilihanSoal = Soal::where('soal', 'Apakah lulusan direkomendasikan?')->firstOrFail();
    $this->assertDatabaseHas('soal', ['id' => $pilihanSoal->id, 'jenis_soal' => 'multiple_choice']);
    $this->assertDatabaseHas('jawaban', ['soal_id' => $pilihanSoal->id, 'jawaban' => 'Ya', 'nilai' => 1]);

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
    ])
        ->assertRedirect('/')
        ->assertSessionHas('success', 'Jawaban Anda telah tersimpan dengan aman.');

    $this->assertDatabaseHas('survey', ['id' => $survey->id, 'is_completed' => true]);
    $this->assertDatabaseHas('respon_jawaban', ['survey_id' => $survey->id, 'jawaban_id' => $jawaban->id]);

    // Kode yang sudah dipakai tidak boleh dapat dibuka atau dikirim ulang.
    $this->get(route('survey.fill', $survey->access_code))->assertForbidden();
    $this->post(route('survey.submit', $survey->access_code), [
        'nama_pengisi' => 'Rina Penyelia',
        'nama_perusahaan' => $perusahaan->nama_perusahaan,
        'jumlah_lulusan_bekerja' => 1,
        'jawaban' => [$soal->id => $jawaban->id],
    ])->assertForbidden();

    $this->assertDatabaseCount('survey_arsip', 1);
    $this->assertDatabaseCount('respon_jawaban', 1);
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

test('bulk survey skips only graduates who already have a survey in the selected period', function () {
    $perusahaan = PenggunaLulusan::create(perusahaanData());
    $lulusanSudahAda = Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Lulusan Sudah Disurvei',
        'nim' => '22410100010',
        'program_studi' => 'Teknik Informatika',
        'fakultas' => 'FTI',
        'program_studi_id' => $this->programStudi->id,
        'fakultas_id' => $this->fakultas->id,
        'tahun_lulus' => '2025-08-01',
        'status' => true,
    ]);
    $lulusanBaru = Lulusan::create([
        'pengguna_lulusan_id' => $perusahaan->id,
        'nama' => 'Lulusan Baru',
        'nim' => '22410100011',
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
    Survey::create([
        'judul' => 'Survey Sebelumnya',
        'tahun' => 2026,
        'periode_id' => $this->periode->id,
        'lulusan_id' => $lulusanSudahAda->id,
        'pengguna_lulusan_id' => $perusahaan->id,
        'access_code' => 'EXISTS202',
        'is_completed' => false,
        'is_active' => true,
    ]);

    $this->actingAs($this->admin)
        ->post(route('survey.bulk.store'), [
            'judul' => 'Survey Massal 2026',
            'periode_id' => $this->periode->id,
            'tahun_lulus' => 2025,
            'soal_pilihan' => [$soal->id],
        ])
        ->assertRedirect(route('survey', ['periode_id' => $this->periode->id]))
        ->assertSessionHas('success', 'Berhasil membuat 1 survey untuk lulusan tahun 2025. 1 lulusan dilewati karena sudah memiliki survei pada periode ini.');

    $this->assertDatabaseCount('survey', 2);
    $this->assertDatabaseHas('survey', [
        'lulusan_id' => $lulusanBaru->id,
        'periode_id' => $this->periode->id,
    ]);
});
