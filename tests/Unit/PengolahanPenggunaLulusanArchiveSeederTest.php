<?php

use Database\Seeders\PengolahanPenggunaLulusanArchiveSeeder;

test('archive seeder reads the uppermost table, including aggregate historical responses', function () {
    $seeder = new PengolahanPenggunaLulusanArchiveSeeder();
    $readWorkbook = new ReflectionMethod($seeder, 'readWorkbook');
    $records = $readWorkbook->invoke($seeder, dirname(__DIR__, 2) . '/docs/Pengolahan Pengguna Lulusan ALL.xlsx');

    $byYear = collect($records)->groupBy('year');

    // Hanya tabel paling atas pada tiap tab yang dibaca. Tabel berikutnya pada
    // tab 2022--2024 dan Grafik Dashboard tidak menjadi sumber data. Tab 2020
    // dan 2021 tidak mencantumkan nama alumni per baris, tetapi respons
    // perusahaan agregatnya tetap merupakan data historis yang valid.
    expect($byYear->keys()->sort()->values()->all())->toBe([2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024])
        ->and($byYear[2022])->toHaveCount(22)
        ->and($byYear[2023])->toHaveCount(16)
        ->and($byYear[2024])->toHaveCount(4)
        ->and($byYear[2024]->pluck('source_row')->all())->toBe([3, 4, 5, 6]);

    foreach (collect($records)->filter(fn (array $record) => ! in_array($record['year'], [2020, 2021], true)) as $record) {
        expect($record['alumni'])->not->toBeNull()
            ->and($record['alumni'])->not->toStartWith('Lulusan Arsip');
    }

    expect($byYear[2020])->not->toBeEmpty()
        ->and($byYear[2021])->not->toBeEmpty()
        ->and($byYear[2020]->every(fn (array $record) => $record['alumni'] === null))->toBeTrue()
        ->and($byYear[2021]->every(fn (array $record) => $record['alumni'] === null))->toBeTrue();
});
