<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportExport
{
    private array $filters;

    // ── Palet Warna Global ─────────────────────────────────────────────────
    private const TITLE_BG    = 'FF1E3A5F'; // Navy gelap – judul utama
    private const SECTION_BG  = 'FF2D3748'; // Biru-abu gelap – divider bagian
    private const ROW_ALT     = 'FFF7F9FC'; // Abu sangat muda – baris genap
    private const BORDER      = 'FFD0D7DE'; // Abu muda – border
    private const INFO_BG     = 'FFECF1F7'; // Abu-biru – blok info prodi
    private const INFO_LBL    = 'FF4A5568'; // Abu-gelap – label info
    private const SUBTOTAL_BG = 'FFFFD966'; // Amber keemasan – baris subtotal kategori
    private const TOTAL_BG    = 'FFE2E8F0'; // Abu-biru muda – baris total
    private const RATA_BG     = 'FFCBD5E1'; // Abu-biru – baris rata-rata

    // Warna header kolom rating (semantik)
    private const RATING_HDR = [
        4 => 'FF1E7E34', // Hijau tua  – Sangat Baik
        3 => 'FF0B5ED7', // Biru tua   – Baik
        2 => 'FFD4690C', // Oranye tua – Kurang
        1 => 'FFBB2D3B', // Merah tua  – Sangat Kurang
    ];

    // Warna aksen per fakultas
    private array $fakultasColors = [
        'FTI'  => 'FF1B4F8A', // Biru navy
        'FDIK' => 'FF7D3C00', // Coklat tua
        'FEB'  => 'FF1E5E2E', // Hijau tua
    ];

    // Warna header data-table (lebih terang dari aksen)
    private array $fakultasHeaderLight = [
        'FTI'  => 'FF2E75B6',
        'FDIK' => 'FFA05000',
        'FEB'  => 'FF2D7A43',
    ];

    private array $nilaiLabel = [
        4 => 'Sangat Baik (4)',
        3 => 'Baik (3)',
        2 => 'Kurang (2)',
        1 => 'Sangat Kurang (1)',
    ];

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $tahunList = $this->getTahunList();

        if (empty($tahunList)) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Tidak Ada Data');
            $sheet->setCellValue('A1', 'Tidak ada data survey yang selesai sesuai filter yang dipilih.');
            return $spreadsheet;
        }

        foreach ($tahunList as $tahun) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle((string) $tahun);
            $this->buildSheet($sheet, $tahun);
        }

        return $spreadsheet;
    }

    private function buildSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $tahun): void
    {
        $row = 1;

        // ── Judul Utama ────────────────────────────────────────────────────
        $sheet->mergeCells("A{$row}:R{$row}");
        $sheet->setCellValue("A{$row}", 'LAPORAN TRACER STUDY — EVALUASI PENGGUNA LULUSAN');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::TITLE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row++;

        $sheet->mergeCells("A{$row}:R{$row}");
        $sheet->setCellValue("A{$row}", 'Universitas Dinamika');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::TITLE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $filterText = "Tahun Lulus: {$tahun}"
            . ($this->filters['fakultas']      ? '   |   Fakultas: ' . $this->filters['fakultas'] : '')
            . ($this->filters['program_studi'] ? '   |   Prodi: '    . $this->filters['program_studi'] : '');
        $sheet->mergeCells("A{$row}:R{$row}");
        $sheet->setCellValue("A{$row}", $filterText);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FFAABCCE']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::TITLE_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;
        $row++; // baris kosong

        $fakultasList = $this->filters['fakultas']
            ? [$this->filters['fakultas']]
            : ['FTI', 'FDIK', 'FEB'];

        $summaryDataPerFakultas = [];

        foreach ($fakultasList as $fakultas) {
            $soalList = $this->getSoalForFakultas($fakultas);
            $dataRows = $this->getDataRows($tahun, $fakultas);

            if ($dataRows->isEmpty()) {
                continue;
            }

            $color      = $this->fakultasColors[$fakultas]       ?? 'FF1B4F8A';
            $colorLight = $this->fakultasHeaderLight[$fakultas]   ?? 'FF2E75B6';
            $colEnd     = $this->colLetter(9 + count($soalList) - 1);

            // ── Header Fakultas ────────────────────────────────────────────
            $sheet->mergeCells("A{$row}:{$colEnd}{$row}");
            $sheet->setCellValue("A{$row}", "FAKULTAS: {$fakultas}");
            $sheet->getStyle("A{$row}:{$colEnd}{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;

            // ── Header Kolom Data ──────────────────────────────────────────
            $headers = ['No', 'Prodi', 'NIM', 'Nama Alumni', 'Nama Responden', 'Nama Perusahaan', 'Jenis Perusahaan', 'Cab. Kota', 'Cab. Negara'];
            foreach ($soalList as $soal) {
                $headers[] = $soal->kode;
            }

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}{$row}", $header);
                $col++;
            }
            $sheet->getStyle("A{$row}:{$colEnd}{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colorLight]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;

            // ── Baris Data ─────────────────────────────────────────────────
            $no         = 1;
            $soalIdList = $soalList->pluck('id')->toArray();
            $peProdi    = [];

            foreach ($dataRows as $lulusan) {
                $col = 'A';
                $sheet->setCellValue("{$col}{$row}", $no++); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->program_studi); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->nim); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->nama); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->nama_penyelia); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->nama_perusahaan); $col++;
                $sheet->setCellValue("{$col}{$row}", ucfirst($lulusan->jenis_perusahaan ?? '-')); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->cabang_kota ? 'Ya' : 'Tidak'); $col++;
                $sheet->setCellValue("{$col}{$row}", $lulusan->cabang_negara ? 'Ya' : 'Tidak'); $col++;

                $jawabanMap = $this->getJawabanForSurvey($lulusan->survey_id, $soalIdList);

                foreach ($soalList as $soal) {
                    $nilai = $jawabanMap[$soal->id] ?? null;
                    $label = $nilai ? ($this->nilaiLabel[$nilai] ?? $nilai) : '-';
                    $sheet->setCellValue("{$col}{$row}", $label);
                    $col++;
                }

                $bgColor = ($no % 2 === 0) ? self::ROW_ALT : 'FFFFFFFF';
                $sheet->getStyle("A{$row}:{$colEnd}{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $peProdi[$lulusan->program_studi][] = $jawabanMap;
                $row++;
            }

            $summaryDataPerFakultas[$fakultas] = [
                'soalList'   => $soalList,
                'peProdi'    => $peProdi,
                'color'      => $color,
                'colorLight' => $colorLight,
            ];

            $row += 2;
        }

        // ── Tabel Ringkasan ────────────────────────────────────────────────
        if (empty($summaryDataPerFakultas)) {
            return;
        }

        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", 'RINGKASAN DISTRIBUSI PENILAIAN PER PROGRAM STUDI');
        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::SECTION_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;
        $row++;

        foreach ($summaryDataPerFakultas as $fakultas => $sData) {
            $soalList   = $sData['soalList'];
            $peProdi    = $sData['peProdi'];
            $color      = $sData['color'];
            $colorLight = $sData['colorLight'];

            foreach ($peProdi as $prodi => $jawabanRows) {
                $jumlahResp = count($jawabanRows);

                // ── Blok Info Prodi ────────────────────────────────────────
                $infoItems = [
                    'Prodi'        => $prodi,
                    'Fakultas'     => $fakultas,
                    'Tahun Lulus'  => $tahun,
                    'Jumlah Resp'  => $jumlahResp,
                ];
                foreach ($infoItems as $label => $value) {
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("B{$row}", $value);
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => self::INFO_LBL]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::INFO_BG]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                    ]);
                    $sheet->getStyle("B{$row}")->applyFromArray([
                        'font'    => ['bold' => ($label === 'Prodi')],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                    ]);
                    $row++;
                }
                $row++; // gap

                // ── Header Distribusi per Soal ─────────────────────────────
                // Kolom A: nama soal (warna aksen fakultas)
                $sheet->setCellValue("A{$row}", 'Kode Soal');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colorLight]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                ]);

                // Kolom B-E: warna semantik rating
                $ratingLabels = [4 => 'Sangat Baik (4)', 3 => 'Baik (3)', 2 => 'Kurang (2)', 1 => 'Sangat Kurang (1)'];
                $ratingCols   = [4 => 'B', 3 => 'C', 2 => 'D', 1 => 'E'];
                foreach ($ratingLabels as $rating => $label) {
                    $rCol = $ratingCols[$rating];
                    $sheet->setCellValue("{$rCol}{$row}", $label);
                    $sheet->getStyle("{$rCol}{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RATING_HDR[$rating]]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                    ]);
                }
                $row++;

                // ── Soal per Kategori + Subtotal Kategori ─────────────────
                $soalPerKategori = [];
                foreach ($soalList as $soal) {
                    $key = $soal->nama_kategori ?? 'Tidak Berkategori';
                    $soalPerKategori[$key][] = $soal;
                }

                $kategoriDistribusi = [];
                $soalNo = 0;

                foreach ($soalPerKategori as $namaKategori => $soalDalamKategori) {
                    $kategoriAccum = [4 => [], 3 => [], 2 => [], 1 => []];

                    foreach ($soalDalamKategori as $soal) {
                        $counts = [4 => 0, 3 => 0, 2 => 0, 1 => 0];
                        foreach ($jawabanRows as $jawabanMap) {
                            $nilai = $jawabanMap[$soal->id] ?? null;
                            if ($nilai && isset($counts[$nilai])) {
                                $counts[$nilai]++;
                            }
                        }
                        $total = array_sum($counts);

                        $pctSB = $total > 0 ? round($counts[4] / $total * 100, 1) : 0;
                        $pctB  = $total > 0 ? round($counts[3] / $total * 100, 1) : 0;
                        $pctK  = $total > 0 ? round($counts[2] / $total * 100, 1) : 0;
                        $pctSK = $total > 0 ? round($counts[1] / $total * 100, 1) : 0;

                        $soalNo++;
                        $rowBg = ($soalNo % 2 === 0) ? self::ROW_ALT : 'FFFFFFFF';

                        $sheet->setCellValue("A{$row}", $soal->kode);
                        $sheet->setCellValue("B{$row}", $pctSB . '%');
                        $sheet->setCellValue("C{$row}", $pctB . '%');
                        $sheet->setCellValue("D{$row}", $pctK . '%');
                        $sheet->setCellValue("E{$row}", $pctSK . '%');
                        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBg]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                        ]);
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                        $kategoriAccum[4][] = $pctSB;
                        $kategoriAccum[3][] = $pctB;
                        $kategoriAccum[2][] = $pctK;
                        $kategoriAccum[1][] = $pctSK;
                        $row++;
                    }

                    // Baris subtotal kategori (amber)
                    $avgSB = count($kategoriAccum[4]) > 0 ? round(array_sum($kategoriAccum[4]) / count($kategoriAccum[4]), 1) : 0;
                    $avgB  = count($kategoriAccum[3]) > 0 ? round(array_sum($kategoriAccum[3]) / count($kategoriAccum[3]), 1) : 0;
                    $avgK  = count($kategoriAccum[2]) > 0 ? round(array_sum($kategoriAccum[2]) / count($kategoriAccum[2]), 1) : 0;
                    $avgSK = count($kategoriAccum[1]) > 0 ? round(array_sum($kategoriAccum[1]) / count($kategoriAccum[1]), 1) : 0;

                    $sheet->setCellValue("A{$row}", $namaKategori);
                    $sheet->setCellValue("B{$row}", $avgSB . '%');
                    $sheet->setCellValue("C{$row}", $avgB . '%');
                    $sheet->setCellValue("D{$row}", $avgK . '%');
                    $sheet->setCellValue("E{$row}", $avgSK . '%');
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FF1A2637']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::SUBTOTAL_BG]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCAA00']]],
                    ]);
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $kategoriDistribusi[$namaKategori] = [4 => $avgSB, 3 => $avgB, 2 => $avgK, 1 => $avgSK];
                    $row++;
                }

                // ── Tabel Ringkasan Tingkat Kepuasan per Kategori ─────────
                $row++;
                $sheet->mergeCells("A{$row}:E{$row}");
                $sheet->setCellValue("A{$row}", 'TINGKAT KEPUASAN PENGGUNA (%)');
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;

                // Header kolom summary — tetap warna semantik per rating
                $sheet->setCellValue("A{$row}", 'Jenis Kemampuan');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colorLight]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                ]);
                foreach ($ratingLabels as $rating => $label) {
                    $rCol = $ratingCols[$rating];
                    $sheet->setCellValue("{$rCol}{$row}", $label);
                    $sheet->getStyle("{$rCol}{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RATING_HDR[$rating]]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                    ]);
                }
                $row++;

                $totalSB = 0; $totalB = 0; $totalK = 0; $totalSK = 0;
                $katNo = 0;
                foreach ($kategoriDistribusi as $namaKategori => $pcts) {
                    $katNo++;
                    $rowBg = ($katNo % 2 === 0) ? self::ROW_ALT : 'FFFFFFFF';
                    $sheet->setCellValue("A{$row}", $namaKategori);
                    $sheet->setCellValue("B{$row}", $pcts[4] . '%');
                    $sheet->setCellValue("C{$row}", $pcts[3] . '%');
                    $sheet->setCellValue("D{$row}", $pcts[2] . '%');
                    $sheet->setCellValue("E{$row}", $pcts[1] . '%');
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                    ]);
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $totalSB += $pcts[4];
                    $totalB  += $pcts[3];
                    $totalK  += $pcts[2];
                    $totalSK += $pcts[1];
                    $row++;
                }

                // Baris Total
                $sheet->setCellValue("A{$row}", 'Total');
                $sheet->setCellValue("B{$row}", round($totalSB, 1) . '%');
                $sheet->setCellValue("C{$row}", round($totalB,  1) . '%');
                $sheet->setCellValue("D{$row}", round($totalK,  1) . '%');
                $sheet->setCellValue("E{$row}", round($totalSK, 1) . '%');
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FF1A2637']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::TOTAL_BG]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                ]);
                $row++;

                // Baris Rata-Rata
                $countKat = count($kategoriDistribusi);
                $sheet->setCellValue("A{$row}", 'Rata - Rata');
                $sheet->setCellValue("B{$row}", ($countKat > 0 ? round($totalSB / $countKat, 1) : 0) . '%');
                $sheet->setCellValue("C{$row}", ($countKat > 0 ? round($totalB  / $countKat, 1) : 0) . '%');
                $sheet->setCellValue("D{$row}", ($countKat > 0 ? round($totalK  / $countKat, 1) : 0) . '%');
                $sheet->setCellValue("E{$row}", ($countKat > 0 ? round($totalSK / $countKat, 1) : 0) . '%');
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FF1A2637']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::RATA_BG]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => self::BORDER]]],
                ]);
                $row++;

                $row += 2;
            }
        }

        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->getColumnDimension('E')->setAutoSize(true);
    }

    private function getTahunList(): array
    {
        $q = DB::table('lulusan')
            ->join('survey', 'lulusan.id', '=', 'survey.lulusan_id')
            ->where('survey.is_completed', true)
            ->selectRaw('EXTRACT(YEAR FROM lulusan.tahun_lulus) as tahun')
            ->distinct();

        if ($this->filters['tahun']) {
            $q->whereRaw('EXTRACT(YEAR FROM lulusan.tahun_lulus) = ?', [$this->filters['tahun']]);
        }
        if ($this->filters['fakultas']) {
            $q->where('lulusan.fakultas', $this->filters['fakultas']);
        }
        if ($this->filters['program_studi']) {
            $q->where('lulusan.program_studi', $this->filters['program_studi']);
        }

        return $q->orderBy('tahun')->pluck('tahun')->map(fn($v) => (int) $v)->toArray();
    }

    private function getSoalForFakultas(string $fakultas)
    {
        return DB::table('soal')
            ->leftJoin('kategoris', 'soal.kategori_id', '=', 'kategoris.id')
            ->where('soal.is_active', true)
            ->where('soal.jenis_soal', 'rating')
            ->where(function ($q) use ($fakultas) {
                $q->where('soal.peruntukan_fakultas', 'Umum')
                  ->orWhere('soal.peruntukan_fakultas', $fakultas);
            })
            ->orderBy('soal.kode')
            ->get(['soal.id', 'soal.kode', 'soal.soal', 'soal.kategori_id', 'kategoris.nama_kategori']);
    }

    private function getDataRows(int $tahun, string $fakultas)
    {
        $q = DB::table('survey')
            ->join('lulusan', 'survey.lulusan_id', '=', 'lulusan.id')
            ->join('pengguna_lulusan', 'survey.pengguna_lulusan_id', '=', 'pengguna_lulusan.id')
            ->where('survey.is_completed', true)
            ->where('lulusan.fakultas', $fakultas)
            ->whereRaw('EXTRACT(YEAR FROM lulusan.tahun_lulus) = ?', [$tahun])
            ->select(
                'survey.id as survey_id',
                'lulusan.nama',
                'lulusan.nim',
                'lulusan.program_studi',
                'pengguna_lulusan.nama_penyelia',
                'pengguna_lulusan.nama_perusahaan',
                'pengguna_lulusan.jenis_perusahaan',
                'pengguna_lulusan.cabang_kota',
                'pengguna_lulusan.cabang_negara'
            )
            ->orderBy('lulusan.program_studi')
            ->orderBy('lulusan.nama');

        if ($this->filters['program_studi']) {
            $q->where('lulusan.program_studi', $this->filters['program_studi']);
        }

        return $q->get();
    }

    private function getJawabanForSurvey(int $surveyId, array $soalIds): array
    {
        $rows = DB::table('respon_jawaban')
            ->join('jawaban', 'respon_jawaban.jawaban_id', '=', 'jawaban.id')
            ->where('respon_jawaban.survey_id', $surveyId)
            ->whereIn('respon_jawaban.soal_id', $soalIds)
            ->select('respon_jawaban.soal_id', 'jawaban.nilai')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->soal_id] = (int) $r->nilai;
        }
        return $map;
    }

    private function colLetter(int $n): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($n);
    }

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->build();

        $tahun    = $this->filters['tahun']          ?? 'Semua';
        $fak      = $this->filters['fakultas']        ?? 'Semua';
        $prodi    = $this->filters['program_studi']   ?? '';
        $parts    = array_filter(["Tahun{$tahun}", "Fak{$fak}", $prodi ? 'Prodi_' . str_replace(' ', '_', $prodi) : '']);
        $filename = 'Laporan_Tracer_Study_' . implode('_', $parts) . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
