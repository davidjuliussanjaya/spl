<?php

use App\Services\SatisfactionScoreService;

it('uses the pure score when the response rate meets the dynamic minimum', function () {
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

it('applies the response-rate penalty when the response rate is below the dynamic minimum', function () {
    $score = app(SatisfactionScoreService::class)->calculateFromPercentages(
        [4 => 75, 3 => 25, 2 => 0, 1 => 0],
        15,
        100,
    );

    expect($score['minimum_response_rate_pct'])->toBe(19.8)
        ->and($score['rumus'])->toBe('Rumus B (Penalti)')
        ->and(round($score['faktor_pembobot'], 6))->toBe(0.757576)
        ->and(round($score['skor_akhir'], 6))->toBe(2.840909);
});

it('calculates the minimum response rate from the alumni population', function () {
    $service = app(SatisfactionScoreService::class);

    expect($service->minimumResponseRate(0))->toBe(0.20)
        ->and($service->minimumResponseRate(2500))->toBe(0.15)
        ->and($service->minimumResponseRate(5000))->toBe(0.10)
        ->and($service->minimumResponseRate(9000))->toBe(0.10);
});
