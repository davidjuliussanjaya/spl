<?php

use App\Services\SatisfactionScoreService;

it('uses the pure score when the response rate meets 30 percent', function () {
    $score = app(SatisfactionScoreService::class)->calculateFromPercentages(
        [4 => 75, 3 => 25, 2 => 0, 1 => 0],
        40,
        100,
    );

    expect($score['rumus'])->toBe('Rumus A (Murni)')
        ->and($score['skor_murni'])->toBe(3.75)
        ->and($score['faktor_pembobot'])->toBe(1.0)
        ->and($score['skor_akhir'])->toBe(3.75);
});

it('applies the response-rate penalty when the response rate is below 30 percent', function () {
    $score = app(SatisfactionScoreService::class)->calculateFromPercentages(
        [4 => 75, 3 => 25, 2 => 0, 1 => 0],
        15,
        100,
    );

    expect($score['rumus'])->toBe('Rumus B (Penalti)')
        ->and($score['faktor_pembobot'])->toBe(0.5)
        ->and($score['skor_akhir'])->toBe(1.875);
});
