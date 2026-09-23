<?php

use App\Services\StudentProgressionService;

test('generation numbers map to the school join year', function () {
    $service = new StudentProgressionService();

    expect($service->generationToJoinYear(1))->toBe(2007)
        ->and($service->generationToJoinYear(16))->toBe(2022);
});

test('join years map back to generation numbers', function () {
    $service = new StudentProgressionService();

    expect($service->joinYearToGeneration(2007))->toBe(1)
        ->and($service->joinYearToGeneration(2022))->toBe(16);
});
