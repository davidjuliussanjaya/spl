<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Mengimpor arsip hasil pengolahan pengguna lulusan dari berkas Excel kampus.
 *
 * Seluruh respons disimpan sebagai snapshot pada survey_arsip. Format jawaban
 * diseragamkan agar laporan 2016--2024 memakai kategori, pertanyaan, dan opsi
 * jawaban yang sama walaupun format formulir sumber berubah dari tahun ke tahun.
 */
class PengolahanPenggunaLulusanArchiveSeeder extends Seeder
{
    private const ARCHIVE_FILE = 'docs/Pengolahan Pengguna Lulusan ALL.xlsx';

    /**
     * Definisi instrumen bersama untuk data arsip dan pertanyaan aktif.
     * Total: 12 kategori dan 35 pertanyaan.
     */
    public static function instrumentDefinition(): array
    {
        $rating = [
            ['jawaban' => 'Sangat Baik', 'nilai' => 4, 'urutan' => 1],
            ['jawaban' => 'Baik', 'nilai' => 3, 'urutan' => 2],
            ['jawaban' => 'Cukup', 'nilai' => 2, 'urutan' => 3],
            ['jawaban' => 'Kurang', 'nilai' => 1, 'urutan' => 4],
        ];

        $questions = fn (string $prefix, array $texts) => collect($texts)
            ->mapWithKeys(fn (string $text, int $index) => [
                $prefix . ($index + 1) => ['teks' => $text, 'jenis' => 'rating', 'pilihan' => $rating],
            ])
            ->all();

        return [
            'B. Etika' => ['deskripsi' => 'Integritas dan profesionalisme lulusan.', 'soal' => $questions('B', [
                'Kejujuran lulusan dalam bekerja', 'Tanggung jawab lulusan terhadap tugas',
                'Profesionalisme lulusan di lingkungan kerja', 'Kepatuhan lulusan pada etika kerja',
            ])],
            'C. Keahlian Berdasarkan Bidang Ilmu' => ['deskripsi' => 'Kompetensi lulusan sesuai bidang ilmu.', 'soal' => $questions('C', [
                'Kesesuaian kompetensi dengan kebutuhan pekerjaan', 'Kemampuan menyelesaikan pekerjaan sesuai bidang',
                'Kemampuan menerapkan pengetahuan dan keterampilan',
            ])],
            'D. Kemampuan Berbahasa Asing' => ['deskripsi' => 'Penguasaan bahasa asing di tempat kerja.', 'soal' => $questions('D', [
                'Kemampuan memahami komunikasi dalam bahasa asing', 'Kemampuan berkomunikasi menggunakan bahasa asing',
            ])],
            'E. Penggunaan Teknologi Informasi' => ['deskripsi' => 'Pemanfaatan teknologi informasi.', 'soal' => $questions('E', [
                'Kemampuan menggunakan teknologi pendukung pekerjaan', 'Kemampuan mempelajari teknologi baru',
                'Kemampuan memanfaatkan teknologi informasi secara efektif',
            ])],
            'F. Kemampuan Berkomunikasi' => ['deskripsi' => 'Komunikasi lulusan di lingkungan kerja.', 'soal' => $questions('F', [
                'Kemampuan menyampaikan ide secara jelas', 'Kemampuan berkomunikasi secara efektif',
                'Kemampuan mendengarkan dan memahami pihak lain', 'Kemampuan berkomunikasi lintas tim',
            ])],
            'G. Kerjasama Tim' => ['deskripsi' => 'Kolaborasi lulusan dalam tim.', 'soal' => $questions('G', [
                'Kemampuan bekerja sama dalam tim', 'Kontribusi untuk mencapai tujuan tim',
                'Kemampuan menghargai peran anggota tim',
            ])],
            'H. Pengembangan Diri' => ['deskripsi' => 'Kemauan lulusan untuk berkembang.', 'soal' => $questions('H', [
                'Kemauan terus belajar', 'Kemampuan beradaptasi terhadap perubahan',
                'Inisiatif mengembangkan kompetensi', 'Kemampuan menerima masukan untuk perbaikan',
            ])],
            'I. Kepemimpinan' => ['deskripsi' => 'Kemampuan lulusan memimpin dan mengambil keputusan.', 'soal' => $questions('I', [
                'Kemampuan mengambil inisiatif', 'Kemampuan mengambil keputusan', 'Kemampuan mengarahkan rekan kerja',
                'Kemampuan menyelesaikan konflik', 'Kemampuan bertanggung jawab sebagai pemimpin',
            ])],
            'J. Etos Kerja' => ['deskripsi' => 'Kedisiplinan dan komitmen lulusan.', 'soal' => $questions('J', [
                'Kedisiplinan dalam menjalankan tugas', 'Ketelitian dalam bekerja',
                'Komitmen terhadap target kerja', 'Ketangguhan menghadapi tekanan kerja',
            ])],
            'K. Evaluasi Umum' => ['deskripsi' => 'Penilaian umum atas kinerja lulusan.', 'soal' => [
                'K1' => ['teks' => 'Secara umum, bagaimana kinerja lulusan Universitas Dinamika di institusi Anda?', 'jenis' => 'multiple_choice', 'pilihan' => $rating],
            ]],
            'L. Bidang yang Perlu Ditingkatkan' => ['deskripsi' => 'Kompetensi lulusan yang perlu diperkuat.', 'soal' => [
                'L1' => ['teks' => 'Bidang apa yang perlu lebih dikuasai lulusan? (dapat memilih lebih dari satu)', 'jenis' => 'multiple_choice', 'pilihan' => [
                    ['jawaban' => 'Etika dan Integritas', 'nilai' => 1, 'urutan' => 1],
                    ['jawaban' => 'Keahlian Berdasarkan Bidang Ilmu', 'nilai' => 2, 'urutan' => 2],
                    ['jawaban' => 'Kemampuan Berbahasa Asing', 'nilai' => 3, 'urutan' => 3],
                    ['jawaban' => 'Penggunaan Teknologi Informasi', 'nilai' => 4, 'urutan' => 4],
                    ['jawaban' => 'Kemampuan Berkomunikasi', 'nilai' => 5, 'urutan' => 5],
                    ['jawaban' => 'Kerjasama Tim', 'nilai' => 6, 'urutan' => 6],
                    ['jawaban' => 'Pengembangan Diri', 'nilai' => 7, 'urutan' => 7],
                    ['jawaban' => 'Kepemimpinan', 'nilai' => 8, 'urutan' => 8],
                    ['jawaban' => 'Etos Kerja', 'nilai' => 9, 'urutan' => 9],
                    ['jawaban' => 'Tidak ada kebutuhan khusus', 'nilai' => 10, 'urutan' => 10],
                ]],
            ]],
            'M. Kriteria Lulusan' => ['deskripsi' => 'Saran kualitatif dari pengguna lulusan.', 'soal' => [
                'M1' => ['teks' => 'Kriteria lulusan seperti apa yang diharapkan institusi Anda?', 'jenis' => 'essay', 'pilihan' => []],
            ]],
        ];
    }

    public function run(): void
    {
        $questionCatalog = $this->ensureInstrument();
        $sourcePath = base_path(self::ARCHIVE_FILE);

        if (! is_file($sourcePath)) {
            throw new \RuntimeException('Berkas arsip tidak ditemukan: ' . self::ARCHIVE_FILE);
        }

        $rows = $this->readWorkbook($sourcePath);
        $imported = 0;

        DB::transaction(function () use ($rows, $questionCatalog, &$imported): void {
            $periodeIds = $this->ensurePeriods();

            foreach ($rows as $record) {
                $answers = $this->buildAnswers($record['cells'], $record['headers'], $questionCatalog);
                $year = $record['year'];
                $alumniIndex = (int) ($record['alumni_index'] ?? 1);
                $recordSuffix = sprintf('%04d', $record['source_row'])
                    . ($alumniIndex > 1 ? '-' . str_pad((string) $alumniIndex, 2, '0', STR_PAD_LEFT) : '');
                $accessCode = sprintf('ARS-%d-%s', $year, $recordSuffix);
                $surveyAccessCode = $this->surveyAccessCode($year, $record['source_row'], $alumniIndex);
                $submittedAt = Carbon::create($year, 12, 31, 12, 0, 0);
                $perusahaanId = $this->upsertPerusahaan($record, $submittedAt);
                $profilAkademik = $this->resolveAcademicProfile($record['program_studi'] ?? null);
                $lulusanId = $this->upsertLulusan($record, $perusahaanId, $submittedAt, $profilAkademik);
                $surveyId = $this->upsertSurvey($record, $surveyAccessCode, $lulusanId, $perusahaanId, $periodeIds[$year], $submittedAt);

                DB::table('survey_arsip')->updateOrInsert(
                    ['access_code' => $accessCode],
                    [
                        'survey_id' => $surveyId,
                        'pengguna_lulusan_id' => $perusahaanId,
                        'judul' => "Arsip Survey Pengguna Lulusan {$year}",
                        'periode_kode' => (string) $year,
                        'periode_nama' => "Periode Survei {$year}",
                        'periode_tanggal_mulai' => "{$year}-01-01",
                        'periode_tanggal_berakhir' => "{$year}-12-31",
                        'submitted_at' => $submittedAt,
                        'tahun_instrumen' => '2024',
                        'lulusan_nama' => $this->displayAlumniName($record),
                        'lulusan_nim' => $record['nim'],
                        'lulusan_program_studi' => $profilAkademik['program_studi']->nama,
                        'lulusan_fakultas' => $profilAkademik['fakultas']->kode,
                        'lulusan_tahun_lulus' => (string) $year,
                        'perusahaan_nama' => $record['perusahaan'],
                        'perusahaan_jenis' => $record['jenis_perusahaan'],
                        'perusahaan_alamat' => $record['alamat'],
                        'perusahaan_kontak' => $record['kontak_perusahaan'],
                        'perusahaan_nomor_badan_hukum' => null,
                        'perusahaan_cabang_kota' => $record['cabang_kota'],
                        'perusahaan_cabang_negara' => $record['cabang_negara'],
                        'penyelia_nama' => $record['responden'],
                        'penyelia_jabatan' => null,
                        'penyelia_email' => $record['email'],
                        'penyelia_kontak' => $record['kontak_penyelia'],
                        'jumlah_lulusan_bekerja' => $record['jumlah_lulusan'],
                        'jawaban_json' => json_encode($answers, JSON_UNESCAPED_UNICODE),
                        'created_at' => $submittedAt,
                        'updated_at' => now(),
                    ],
                );
                $imported++;
            }
        });

        $years = collect($rows)->pluck('year')->unique()->sort()->implode(', ');
        $this->command->info("Arsip pengguna lulusan berhasil di-seed: {$imported} respons ({$years}).");
    }

    /** Membuat seluruh periode yang dapat dipilih di menu Survei. */
    private function ensurePeriods(): array
    {
        $ids = [];
        $now = now();

        foreach (range(2016, 2024) as $year) {
            DB::table('periode')->updateOrInsert(
                ['kode_periode' => (string) $year],
                [
                    'nama_periode' => "Periode Survei {$year}",
                    'tanggal_mulai' => "{$year}-01-01",
                    'tanggal_berakhir' => "{$year}-12-31",
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
            $ids[$year] = DB::table('periode')->where('kode_periode', (string) $year)->value('id');
        }

        return $ids;
    }

    /** Menghubungkan setiap arsip ke perusahaan agar sesi surveinya dapat dibuka dari menu Survei. */
    private function upsertPerusahaan(array $record, Carbon $timestamp): int
    {
        $email = $record['email'] ?? sprintf('arsip.%d.%d@pengguna-lulusan.local', $record['year'], $record['source_row']);

        DB::table('pengguna_lulusan')->updateOrInsert(
            ['email_penyelia' => $email],
            [
                'nama_perusahaan' => $record['perusahaan'],
                'nama_penyelia' => $record['responden'] ?? 'Responden Arsip',
                'jabatan_penyelia' => 'Arsip Pengguna Lulusan',
                'kontak_penyelia' => $record['kontak_penyelia'],
                'jumlah_lulusan' => $this->numberOrNull($record['jumlah_lulusan']),
                'durasi_lulusan_bekerja' => null,
                'nomor_badan_hukum' => null,
                'alamat_perusahaan' => $record['alamat'],
                'kontak_perusahaan' => $record['kontak_perusahaan'],
                'jenis_perusahaan' => $record['jenis_perusahaan'],
                'cabang_kota' => $this->yesNoToInteger($record['cabang_kota']),
                'cabang_negara' => $this->yesNoToInteger($record['cabang_negara']),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );

        return (int) DB::table('pengguna_lulusan')->where('email_penyelia', $email)->value('id');
    }

    /** Menghubungkan setiap arsip ke lulusan agar relasi Survey lengkap. */
    private function upsertLulusan(array $record, int $perusahaanId, Carbon $timestamp, array $profilAkademik): int
    {
        // Tab 2020 dan 2021 mencatat satu respons perusahaan secara agregat,
        // tanpa identitas alumni. Tetap buat relasi teknis yang jelas ditandai
        // sebagai agregat agar respons historisnya tidak hilang.
        $alumniIndex = (int) ($record['alumni_index'] ?? 1);
        $nim = $record['nim'] ?? sprintf('ARS%d%04d', $record['year'], $record['source_row'])
            . ($alumniIndex > 1 ? '-' . str_pad((string) $alumniIndex, 2, '0', STR_PAD_LEFT) : '');
        $nama = $this->displayAlumniName($record);
        $fakultas = $profilAkademik['fakultas'];
        $programStudi = $profilAkademik['program_studi'];

        DB::table('lulusan')->updateOrInsert(
            ['nim' => $nim],
            [
                'pengguna_lulusan_id' => $perusahaanId,
                'nama' => $nama,
                'program_studi' => $programStudi->nama,
                'fakultas' => $fakultas->kode,
                'program_studi_id' => $programStudi->id,
                'fakultas_id' => $fakultas->id,
                'is_aggregate' => empty($record['alumni']),
                'tahun_lulus' => "{$record['year']}-08-15",
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );

        return (int) DB::table('lulusan')->where('nim', $nim)->value('id');
    }

    private function resolveAcademicProfile(?string $rawProgramStudi): array
    {
        $rawProgramStudi = trim((string) $rawProgramStudi);
        $normalized = Str::lower($rawProgramStudi);
        $mappings = [
            ['sistem informasi', 'FTI', 'Fakultas Teknologi dan Informatika', 'Sistem Informasi'],
            ['teknik komputer', 'FTI', 'Fakultas Teknologi dan Informatika', 'Teknik Komputer'],
            ['akuntansi', 'FEB', 'Fakultas Ekonomi dan Bisnis', 'Akuntansi'],
            ['manajemen informatika', 'FTI', 'Fakultas Teknologi dan Informatika', 'Manajemen Informatika'],
            ['manajemen', 'FEB', 'Fakultas Ekonomi dan Bisnis', 'Manajemen'],
            ['desain komunikasi visual', 'FDIK', 'Fakultas Desain dan Industri Kreatif', 'Desain Komunikasi Visual'],
            ['desain produk', 'FDIK', 'Fakultas Desain dan Industri Kreatif', 'Desain Produk'],
            ['produksi film', 'FDIK', 'Fakultas Desain dan Industri Kreatif', 'Produksi Film dan Televisi'],
            ['administrasi perkantoran', 'FDIK', 'Fakultas Desain dan Industri Kreatif', 'Administrasi Perkantoran'],
        ];

        foreach ($mappings as [$needle, $kodeFakultas, $namaFakultas, $namaProdi]) {
            if (Str::contains($normalized, $needle)) {
                return $this->firstOrCreateAcademicProfile($kodeFakultas, $namaFakultas, $namaProdi);
            }
        }

        return $this->firstOrCreateAcademicProfile(
            'UNK',
            'Fakultas Belum Teridentifikasi',
            $rawProgramStudi ?: 'Program Studi Belum Tercatat',
        );
    }

    private function firstOrCreateAcademicProfile(string $kodeFakultas, string $namaFakultas, string $namaProdi): array
    {
        $fakultas = Fakultas::firstOrCreate(['kode' => $kodeFakultas], ['nama' => $namaFakultas]);
        $programStudi = ProgramStudi::firstOrCreate(
            ['fakultas_id' => $fakultas->id, 'nama' => $namaProdi],
            ['kode' => 'PS-' . strtoupper(substr(md5($fakultas->id . '|' . $namaProdi), 0, 12))],
        );

        return [
            'fakultas' => $fakultas,
            'program_studi' => $programStudi,
        ];
    }

    private function displayAlumniName(array $record): string
    {
        return $record['alumni'] ?? sprintf(
            'Data agregat tanpa nama alumni (%d, baris %d)',
            $record['year'],
            $record['source_row'],
        );
    }

    /** Kode survey dibatasi 10 karakter; huruf pertama membedakan alumni dari baris sumber yang sama. */
    private function surveyAccessCode(int $year, int $sourceRow, int $alumniIndex): string
    {
        if ($alumniIndex > 26) {
            throw new \RuntimeException("Terlalu banyak alumni pada baris sumber {$sourceRow} tahun {$year}.");
        }

        return chr(64 + $alumniIndex)
            . substr((string) $year, -2)
            . str_pad((string) $sourceRow, 7, '0', STR_PAD_LEFT);
    }

    /** Membuat sesi survei selesai agar arsip ikut tampil pada menu Survei. */
    private function upsertSurvey(
        array $record,
        string $accessCode,
        int $lulusanId,
        int $perusahaanId,
        int $periodeId,
        Carbon $timestamp,
    ): int {
        DB::table('survey')->updateOrInsert(
            ['access_code' => $accessCode],
            [
                'lulusan_id' => $lulusanId,
                'pengguna_lulusan_id' => $perusahaanId,
                'judul' => "Arsip Survey Pengguna Lulusan {$record['year']}",
                'tahun' => $record['year'],
                'periode_id' => $periodeId,
                'deskripsi' => 'Sesi survei selesai yang diimpor dari arsip pengguna lulusan.',
                'is_completed' => true,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );

        return (int) DB::table('survey')->where('access_code', $accessCode)->value('id');
    }

    /** @return array<string, array{kode:string,kategori:string,soal:string,jenis:string,pilihan:array}> */
    private function ensureInstrument(): array
    {
        $definition = self::instrumentDefinition();
        $now = now();
        $catalog = [];

        DB::transaction(function () use ($definition, $now, &$catalog): void {
            $instrumentId = DB::table('instrumen')->updateOrInsert(
                ['tahun' => 2024],
                [
                    'judul' => 'Instrumen Arsip Pengguna Lulusan 2014-2024',
                    'deskripsi' => 'Instrumen standar untuk normalisasi arsip pengguna lulusan.',
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
            $instrumentId = DB::table('instrumen')->where('tahun', 2024)->value('id');

            foreach ($definition as $categoryName => $categoryData) {
                DB::table('kategoris')->updateOrInsert(
                    ['nama_kategori' => $categoryName],
                    ['deskripsi' => $categoryData['deskripsi'], 'updated_at' => $now, 'created_at' => $now],
                );
                $categoryId = DB::table('kategoris')->where('nama_kategori', $categoryName)->value('id');

                foreach ($categoryData['soal'] as $code => $question) {
                    DB::table('soal')->updateOrInsert(
                        ['kode' => $code],
                        [
                            'instrumen_id' => $instrumentId,
                            'soal' => $question['teks'],
                            'kategori_id' => $categoryId,
                            'jenis_soal' => $question['jenis'],
                            'is_required' => true,
                            'is_active' => true,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ],
                    );
                    $soalId = DB::table('soal')->where('kode', $code)->value('id');

                    foreach ($question['pilihan'] as $choice) {
                        DB::table('jawaban')->updateOrInsert(
                            ['soal_id' => $soalId, 'jawaban' => $choice['jawaban']],
                            ['nilai' => $choice['nilai'], 'urutan' => $choice['urutan'], 'updated_at' => $now, 'created_at' => $now],
                        );
                    }

                    $catalog[$code] = [
                        'kode' => $code,
                        'kategori' => $categoryName,
                        'soal' => $question['teks'],
                        'jenis' => $question['jenis'],
                        'pilihan' => $question['pilihan'],
                    ];
                }
            }

            DB::table('soal')->whereNotIn('kode', array_keys($catalog))->update(['is_active' => false, 'updated_at' => $now]);
        });

        return $catalog;
    }

    /**
     * Membaca hanya Excel Table yang posisinya paling atas dari setiap tab 2016--2024.
     * Tabel lain pada tab yang sama serta sheet Grafik Dashboard sengaja tidak dibaca.
     * Baris agregat tanpa nama alumni (format 2020--2021) tetap dipertahankan.
     *
     * @return array<int, array{year:int,source_row:int,cells:array,headers:array,alumni:?string,nim:?string,program_studi:?string,responden:?string,perusahaan:string,jenis_perusahaan:?string,alamat:?string,kontak_perusahaan:?string,kontak_penyelia:?string,email:?string,jumlah_lulusan:?string,cabang_kota:?string,cabang_negara:?string}>
     */
    private function readWorkbook(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Berkas Excel arsip tidak dapat dibuka.');
        }

        $strings = $this->sharedStrings($zip);
        $workbook = simplexml_load_string((string) $zip->getFromName('xl/workbook.xml'));
        $relationships = simplexml_load_string((string) $zip->getFromName('xl/_rels/workbook.xml.rels'));
        $targets = [];
        foreach ($relationships->Relationship as $relationship) {
            $targets[(string) $relationship['Id']] = 'xl/' . (string) $relationship['Target'];
        }

        $relationshipNamespace = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $records = [];
        foreach ($workbook->sheets->sheet as $sheetMeta) {
            if (! preg_match('/^Th\\. Lulus (20\\d{2})$/', (string) $sheetMeta['name'], $matches)) {
                continue;
            }

            $relationship = $sheetMeta->attributes($relationshipNamespace);
            $sheetPath = $targets[(string) $relationship['id']];
            $sheet = simplexml_load_string((string) $zip->getFromName($sheetPath));
            $bounds = $this->firstTableBounds($zip, $sheetPath, $sheet);
            if ($bounds === null) {
                continue;
            }

            $year = (int) $matches[1];
            $headers = [];
            foreach ($sheet->sheetData->row as $row) {
                $rowNumber = (int) $row['r'];
                if ($rowNumber === $bounds['header_row']) {
                    $headers = $this->rowCells($row, $strings);
                    continue;
                }
                if ($rowNumber <= $bounds['header_row'] || $rowNumber > $bounds['last_row']) {
                    continue;
                }

                $cells = $this->rowCells($row, $strings);
                $record = $this->normaliseRecord($year, (int) $row['r'], $cells, $headers);
                if ($record !== null) {
                    foreach ($this->expandAlumniRecords($record) as $alumniRecord) {
                        $records[] = $alumniRecord;
                    }
                }
            }
        }
        $zip->close();

        return $records;
    }

    /** @return array{header_row:int,last_row:int}|null */
    private function firstTableBounds(ZipArchive $zip, string $sheetPath, \SimpleXMLElement $sheet): ?array
    {
        $relationshipNamespace = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $relationshipPath = dirname($sheetPath) . '/_rels/' . basename($sheetPath) . '.rels';
        $relationshipsXml = $zip->getFromName($relationshipPath);
        if ($relationshipsXml === false) {
            return null;
        }

        $relationships = simplexml_load_string($relationshipsXml);
        $tablePaths = [];
        foreach ($relationships->Relationship as $relationship) {
            if (str_contains((string) $relationship['Type'], '/table')) {
                $tablePaths[(string) $relationship['Id']] = 'xl/' . ltrim(str_replace('../', '', (string) $relationship['Target']), '/');
            }
        }

        $tables = [];
        foreach ($sheet->tableParts->tablePart as $tablePart) {
            $relationship = $tablePart->attributes($relationshipNamespace);
            $tablePath = $tablePaths[(string) $relationship['id']] ?? null;
            if ($tablePath === null || ($tableXml = $zip->getFromName($tablePath)) === false) {
                continue;
            }

            $table = simplexml_load_string($tableXml);
            if (! preg_match('/^[A-Z]+(\d+):[A-Z]+(\d+)$/', (string) $table['ref'], $range)) {
                continue;
            }
            $tables[] = ['header_row' => (int) $range[1], 'last_row' => (int) $range[2]];
        }

        if ($tables === []) {
            return null;
        }

        usort($tables, fn (array $left, array $right) => $left['header_row'] <=> $right['header_row']);

        return $tables[0];
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = simplexml_load_string((string) $zip->getFromName('xl/sharedStrings.xml'));
        $strings = [];
        foreach ($xml->si as $item) {
            $strings[] = trim(strip_tags($item->asXML()));
        }

        return $strings;
    }

    private function rowCells(\SimpleXMLElement $row, array $strings): array
    {
        $cells = [];
        foreach ($row->c as $cell) {
            $column = preg_replace('/\\d+/', '', (string) $cell['r']);
            $value = (string) $cell->v;
            if ((string) $cell['t'] === 's') {
                $value = $strings[(int) $value] ?? '';
            } elseif ((string) $cell['t'] === 'inlineStr') {
                $value = trim(strip_tags($cell->asXML()));
            }
            $cells[$column] = trim($value);
        }

        return $cells;
    }

    private function normaliseRecord(int $year, int $sourceRow, array $cells, array $headers): ?array
    {
        $get = fn (string $column) => $this->nullable($cells[$column] ?? null);

        if ($year <= 2019) {
            $record = [
                'alumni' => $get('F'), 'nim' => $get('E'), 'program_studi' => $get('D'), 'responden' => $get('G'),
                'perusahaan' => $get('J'), 'alamat' => $get('K'), 'kontak_perusahaan' => $get('L'),
                'jenis_perusahaan' => $get('M'), 'jumlah_lulusan' => $get('N'), 'kontak_penyelia' => $get('H'),
                'email' => $get('I'), 'cabang_kota' => null, 'cabang_negara' => null,
            ];
        } elseif ($year === 2020) {
            $record = [
                'alumni' => null, 'nim' => null, 'program_studi' => $get('D'), 'responden' => null,
                'perusahaan' => $get('E'), 'alamat' => $get('F'), 'kontak_perusahaan' => $get('G'),
                'jenis_perusahaan' => $get('H'), 'jumlah_lulusan' => $get('I'), 'kontak_penyelia' => null,
                'email' => null, 'cabang_kota' => null, 'cabang_negara' => null,
            ];
        } elseif ($year === 2021) {
            $record = [
                'alumni' => null, 'nim' => null, 'program_studi' => $get('B'), 'responden' => $get('C'),
                'perusahaan' => $get('D'), 'alamat' => null, 'kontak_perusahaan' => null,
                'jenis_perusahaan' => null, 'jumlah_lulusan' => null, 'kontak_penyelia' => null,
                'email' => null, 'cabang_kota' => null, 'cabang_negara' => null,
            ];
        } else {
            $record = [
                'alumni' => $get('C'), 'nim' => null, 'program_studi' => $get('B'), 'responden' => $get('D'),
                'perusahaan' => $get('E'), 'alamat' => null, 'kontak_perusahaan' => null,
                'jenis_perusahaan' => $get('F'), 'jumlah_lulusan' => null, 'kontak_penyelia' => null,
                'email' => null, 'cabang_kota' => $get('G'), 'cabang_negara' => $get('H'),
            ];
        }

        // Pertahankan hanya baris respons yang memuat program studi dan perusahaan.
        if ($record['perusahaan'] === null || ! preg_match('/(?:^S[1-4]\\b|^D[1-4]\\b|sarjana|diploma)/i', (string) $record['program_studi'])) {
            return null;
        }
        return array_merge($record, ['year' => $year, 'source_row' => $sourceRow, 'cells' => $cells, 'headers' => $headers]);
    }

    /**
     * Satu baris Excel kadang memuat beberapa alumni yang dipisahkan koma.
     * Setiap nama dibuat sebagai satu record agar tidak tampil bertumpuk pada
     * laporan. NIM yang sudah tersedia tidak dipecah karena merujuk pada satu
     * alumni tertentu.
     */
    private function expandAlumniRecords(array $record): array
    {
        if (filled($record['nim']) || ! filled($record['alumni'])) {
            return [array_merge($record, ['alumni_index' => 1])];
        }

        $names = $this->splitAlumniNames($record['alumni']);

        return collect($names)
            ->values()
            ->map(fn (string $name, int $index) => array_merge($record, [
                'alumni' => $name,
                'alumni_index' => $index + 1,
            ]))
            ->all();
    }

    /** @return array<int, string> */
    private function splitAlumniNames(string $rawNames): array
    {
        // Koma pada gelar tidak menandakan alumni berikutnya, misalnya
        // "Risqika Sari, A.Md.". Lindungi bagian itu sebelum memisah daftar.
        $protectedNames = preg_replace_callback(
            '/,\s*((?:A|S|M|D)\.?\s*(?:Md|Kom|T|E|Si|Sn|Pd|H|Hum|Ikom)\.?(?:\s*,\s*(?:M|S)\.?\s*(?:Kom|T|E|Si|Sn|Pd|H|Hum|Ikom)\.?)?)/iu',
            fn (array $matches) => '¦' . $matches[1],
            $rawNames,
        );

        $names = preg_split('/(?:\R|;|,)\s+(?=\p{Lu})/u', (string) $protectedNames) ?: [];

        return collect($names)
            ->map(fn (string $name) => trim(str_replace('¦', ', ', $name)))
            ->filter()
            ->values()
            ->all();
    }

    private function buildAnswers(array $cells, array $headers, array $catalog): array
    {
        $answers = [];

        foreach ($catalog as $code => $question) {
            $source = match ($code) {
                'K1' => $this->findByHeader($headers, $cells, 'kinerja lulusan') ?? $cells['BC'] ?? null,
                'L1' => $this->findByHeader($headers, $cells, 'bidang apa yang kurang') ?? $cells['BD'] ?? null,
                'M1' => $this->findByHeader($headers, $cells, 'kriteria lulusan') ?? $cells['BE'] ?? null,
                default => $this->ratingSource($code, $headers, $cells),
            };

            if ($question['jenis'] === 'essay') {
                $answer = $this->nullable($source);
                if ($answer === null) {
                    continue;
                }
                $score = null;
            } elseif ($code === 'L1') {
                if ($this->nullable($source) === null) {
                    continue;
                }
                $answer = $this->normaliseImprovementAreas((string) $source);
                $score = null;
            } else {
                $rating = $this->normaliseRating($source);
                if ($rating === null) {
                    continue;
                }
                [$answer, $score] = $rating;
            }

            $answers[] = [
                'kode' => $code,
                'kategori' => $question['kategori'],
                'soal' => $question['soal'],
                'jenis' => $question['jenis'],
                'jawaban' => $answer,
                'nilai' => $score,
            ];
        }

        return $answers;
    }

    private function ratingSource(string $code, array $headers, array $cells): ?string
    {
        $candidates = [$code];
        if (preg_match('/^I([1-5])$/', $code, $matches)) {
            $candidates = ['I1.' . $matches[1], 'I. a' . $matches[1]];
        } elseif (preg_match('/^J([1-4])$/', $code, $matches)) {
            $candidates = ['I2.' . $matches[1], 'I. b' . $matches[1]];
        }

        foreach ($candidates as $candidate) {
            $column = array_search($candidate, $headers, true);
            if ($column !== false && $this->nullable($cells[$column] ?? null) !== null) {
                return $cells[$column];
            }
        }

        return null;
    }

    private function findByHeader(array $headers, array $cells, string $needle): ?string
    {
        foreach ($headers as $column => $header) {
            if (str_contains(mb_strtolower($header), $needle)) {
                return $cells[$column] ?? null;
            }
        }

        return null;
    }

    private function normaliseRating(?string $value): ?array
    {
        $value = mb_strtoupper(trim((string) $value));
        return match (true) {
            str_contains($value, 'SANGAT BAIK') || $value === 'SB' => ['Sangat Baik', 4],
            str_contains($value, 'CUKUP') => ['Cukup', 2],
            str_contains($value, 'KURANG') || $value === 'K' => ['Kurang', 1],
            str_contains($value, 'BAIK') || $value === 'B' => ['Baik', 3],
            default => null,
        };
    }

    private function normaliseImprovementAreas(string $value): array
    {
        $value = mb_strtolower($value);
        if ($value === '' || str_contains($value, 'tidak ada') || $value === '-') {
            return ['Tidak ada kebutuhan khusus'];
        }

        $map = [
            'etika' => 'Etika dan Integritas', 'integritas' => 'Etika dan Integritas',
            'bidang ilmu' => 'Keahlian Berdasarkan Bidang Ilmu', 'keahlian' => 'Keahlian Berdasarkan Bidang Ilmu',
            'bahasa' => 'Kemampuan Berbahasa Asing', 'inggris' => 'Kemampuan Berbahasa Asing',
            'teknologi' => 'Penggunaan Teknologi Informasi', 'informasi' => 'Penggunaan Teknologi Informasi', 'it' => 'Penggunaan Teknologi Informasi',
            'komunikasi' => 'Kemampuan Berkomunikasi', 'kerja sama' => 'Kerjasama Tim', 'team' => 'Kerjasama Tim',
            'pengembangan' => 'Pengembangan Diri', 'kepemimpinan' => 'Kepemimpinan', 'etos' => 'Etos Kerja',
        ];
        $areas = [];
        foreach ($map as $keyword => $area) {
            if (str_contains($value, $keyword)) {
                $areas[] = $area;
            }
        }

        return array_values(array_unique($areas)) ?: ['Tidak ada kebutuhan khusus'];
    }

    private function numberOrNull(?string $value): ?int
    {
        $number = filter_var($value, FILTER_VALIDATE_INT);

        return $number === false ? null : $number;
    }

    private function yesNoToInteger(?string $value): int
    {
        return in_array(mb_strtolower(trim((string) $value)), ['ya', 'y', '1', 'true'], true) ? 1 : 0;
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' || $value === '-' ? null : $value;
    }
}
