<?php

namespace App\Services;

/**
 * Menghitung skor kepuasan pengguna lulusan sesuai rumus BAN-PT/LAM.
 *
 * Skor murni memakai skala 1--4. Jika response rate (NL/NJ) kurang dari
 * 30%, skor tersebut dikalikan faktor kecukupan: response rate / 30%.
 */
class SatisfactionScoreService
{
    public const MINIMUM_RESPONSE_RATE = 0.30;

    /**
     * @param array<int, int|float> $ratings Nilai rating mentah pada skala 1--4.
     */
    public function calculate(array $ratings, int $jumlahResponden, int $totalLulusan): array
    {
        $counts = [4 => 0, 3 => 0, 2 => 0, 1 => 0];

        foreach ($ratings as $rating) {
            $nilai = (int) $rating;
            if (array_key_exists($nilai, $counts)) {
                $counts[$nilai]++;
            }
        }

        $totalRating = array_sum($counts);
        $persentase = collect($counts)
            ->map(fn (int $jumlah) => $totalRating > 0 ? ($jumlah / $totalRating) * 100 : 0)
            ->all();

        return $this->calculateFromPercentages($persentase, $jumlahResponden, $totalLulusan, $counts);
    }

    /**
     * @param array<int, int|float> $persentase Distribusi dalam satuan persen (0--100).
     * @param array<int, int>|null $counts
     */
    public function calculateFromPercentages(
        array $persentase,
        int $jumlahResponden,
        int $totalLulusan,
        ?array $counts = null,
    ): array {
        $p4 = (float) ($persentase[4] ?? 0);
        $p3 = (float) ($persentase[3] ?? 0);
        $p2 = (float) ($persentase[2] ?? 0);
        $p1 = (float) ($persentase[1] ?? 0);

        $skorMurni = (4 * $p4 + 3 * $p3 + 2 * $p2 + $p1) / 100;
        $responseRate = $totalLulusan > 0 ? $jumlahResponden / $totalLulusan : 0;
        $sampelCukup = $responseRate >= self::MINIMUM_RESPONSE_RATE;
        $faktorPembobot = $sampelCukup ? 1.0 : $responseRate / self::MINIMUM_RESPONSE_RATE;

        return [
            'jumlah_responden' => $jumlahResponden,
            'total_lulusan' => $totalLulusan,
            'response_rate' => $responseRate,
            'response_rate_pct' => $responseRate * 100,
            'faktor_pembobot' => $faktorPembobot,
            'rumus' => $sampelCukup ? 'Rumus A (Murni)' : 'Rumus B (Penalti)',
            'status_kecukupan' => $sampelCukup ? 'Cukup (≥ 30%)' : 'Kurang (< 30%)',
            'skor_murni' => $skorMurni,
            'skor_akhir' => $skorMurni * $faktorPembobot,
            'counts' => $counts ?? [4 => 0, 3 => 0, 2 => 0, 1 => 0],
            'persentase' => [4 => $p4, 3 => $p3, 2 => $p2, 1 => $p1],
        ];
    }
}
