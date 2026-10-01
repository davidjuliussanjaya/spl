<?php

namespace App\Exports;

use App\Models\Periode;
use App\Models\Survey;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveyPeriodAccessExport extends DefaultValueBinder implements FromCollection, WithColumnWidths, WithCustomValueBinder, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private readonly Periode $periode,
        private readonly string $landingUrl,
    ) {
    }

    public function collection(): Collection
    {
        return Survey::query()
            ->with([
                'penggunalulusan:id,nama_perusahaan,nama_penyelia,kontak_penyelia,jabatan_penyelia',
                'lulusan:id,nama',
            ])
            ->where('periode_id', $this->periode->id)
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Perusahaan',
            'Nama Responden',
            'No. Telp Responden',
            'Jabatan',
            'Lulusan yang Dinilai',
            'Link Website',
            'Kode Akses',
        ];
    }

    public function map($survey): array
    {
        static $nomor = 0;
        $nomor++;

        return [
            $nomor,
            $survey->penggunalulusan?->nama_perusahaan ?? '-',
            $survey->penggunalulusan?->nama_penyelia ?? '-',
            $survey->penggunalulusan?->kontak_penyelia ?? '-',
            $survey->penggunalulusan?->jabatan_penyelia ?? '-',
            $survey->lulusan?->nama ?? '-',
            $this->landingUrl,
            $survey->access_code,
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), ['D', 'H'], true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 8, 'B' => 30, 'C' => 28, 'D' => 22, 'E' => 28, 'F' => 30, 'G' => 38, 'H' => 18];
    }

    public function title(): string
    {
        return 'Akses Survei';
    }
}
