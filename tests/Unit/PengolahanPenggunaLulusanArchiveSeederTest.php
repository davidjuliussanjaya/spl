<?php

use Database\Seeders\PengolahanPenggunaLulusanArchiveSeeder;

test('archive seeder reads only the uppermost table and never creates records from aggregate rows', function () {
    $seeder = new PengolahanPenggunaLulusanArchiveSeeder();
    $readWorkbook = new ReflectionMethod($seeder, 'readWorkbook');
    $records = $readWorkbook->invoke($seeder, dirname(__DIR__, 2) . '/docs/Pengolahan Pengguna Lulusan ALL.xlsx');

    $byYear = collect($records)->groupBy('year');

    // Hanya tabel paling atas pada tiap tab yang dibaca. Tabel berikutnya pada
    // tab 2022--2024 dan Grafik Dashboard tidak menjadi sumber data.
    expect($records)->toHaveCount(198)
        ->and($byYear->keys()->sort()->values()->all())->toBe([2016, 2017, 2018, 2019, 2022, 2023, 2024])
        ->and($byYear[2022])->toHaveCount(22)
        ->and($byYear[2023])->toHaveCount(16)
        ->and($byYear[2024])->toHaveCount(4)
        ->and($byYear[2024]->pluck('source_row')->all())->toBe([3, 4, 5, 6]);

    foreach ($records as $record) {
        expect($record['alumni'])->not->toBeNull()
            ->and($record['alumni'])->not->toStartWith('Lulusan Arsip');
    }
});
