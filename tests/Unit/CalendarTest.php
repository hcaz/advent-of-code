<?php

declare(strict_types=1);

use Aoc\Calendar;

it('gives every past year all 25 days', function () {
    $days = Calendar::releasedDays(new DateTimeImmutable('2026-11-10 12:00:00'));

    $byYear = [];
    foreach ($days as [$year, $day]) {
        $byYear[$year][] = $day;
    }

    expect(array_keys($byYear))->toBe(range(2015, 2025))
        ->and($byYear[2015])->toBe(range(1, 25))
        ->and($byYear[2025])->toBe(range(1, 25))
        ->and($days)->toHaveCount(275);
});

it('releases no days in the current year outside December', function () {
    $days = Calendar::releasedDays(new DateTimeImmutable('2026-11-10 12:00:00'));

    expect(collect($days)->where(0, 2026))->toBeEmpty();
});

it('releases only the days unlocked so far during December', function () {
    $days = Calendar::releasedDays(new DateTimeImmutable('2026-12-07 08:00:00'));

    expect(collect($days)->where(0, 2026)->pluck(1)->all())->toBe(range(1, 7));
});

it('caps the current year at 25 days after Christmas', function () {
    $days = Calendar::releasedDays(new DateTimeImmutable('2026-12-31 23:00:00'));

    expect(collect($days)->where(0, 2026)->pluck(1)->all())->toBe(range(1, 25));
});

it('derives slugs, paths and links without storing them', function () {
    expect(Calendar::slug(2024, 1))->toBe('2024-01')
        ->and(Calendar::path(2024, 1))->toBe('source/_problems/2024-01.md')
        ->and(Calendar::link(2024, 1))->toBe('https://adventofcode.com/2024/day/1')
        ->and(Calendar::link(2015, 25))->toBe('https://adventofcode.com/2015/day/25');
});

it('treats a day solved in its own event window as not retroactive', function () {
    expect(Calendar::isRetroactive('2022-12-09', 2022))->toBeFalse()
        ->and(Calendar::isRetroactive('2022-12-01', 2022))->toBeFalse()
        ->and(Calendar::isRetroactive('2022-12-25', 2022))->toBeFalse();
});

it('treats a day solved outside its own event window as retroactive', function () {
    // A 2015 puzzle solved during December 2022 is still retroactive.
    expect(Calendar::isRetroactive('2022-12-10', 2015))->toBeTrue()
        ->and(Calendar::isRetroactive('2024-07-01', 2023))->toBeTrue()
        ->and(Calendar::isRetroactive('2022-12-26', 2022))->toBeTrue()
        ->and(Calendar::isRetroactive('2022-11-30', 2022))->toBeTrue();
});

it('does not label an unsolved day as retroactive', function () {
    expect(Calendar::isRetroactive(null, 2024))->toBeFalse()
        ->and(Calendar::isRetroactive('', 2024))->toBeFalse()
        ->and(Calendar::isRetroactive('nonsense', 2024))->toBeFalse();
});

it('recognises both generations of stub title', function () {
    expect(Calendar::isStubTitle('Day 01 - 2016'))->toBeTrue()
        ->and(Calendar::isStubTitle('Day 20 - 2015'))->toBeTrue()
        ->and(Calendar::isStubTitle('Day 01:'))->toBeTrue()
        ->and(Calendar::isStubTitle('Day 25:'))->toBeTrue()
        ->and(Calendar::isStubTitle(''))->toBeTrue();
});

it('does not mistake a real title for a stub', function () {
    expect(Calendar::isStubTitle('Day 1: Not Quite Lisp'))->toBeFalse()
        ->and(Calendar::isStubTitle('Day 7: No Space Left On Device'))->toBeFalse()
        ->and(Calendar::isStubTitle('Historian Hysteria'))->toBeFalse();
});
