<?php

declare(strict_types=1);

use Aoc\Calendar;
use Aoc\ProblemFile;
use Aoc\Runner;
use Aoc\Solution;

/**
 * Structural invariants over source/_problems/.
 *
 * These run in CI, where the git-crypt key is absent and puzzle inputs cannot
 * be decrypted. So nothing here executes a solution -- it only checks the shape
 * of the committed files. Actual answer verification is a separate local group.
 */
function problemFiles(): array
{
    return array_map(
        fn (array $pair) => ProblemFile::read(Calendar::path(...$pair)),
        Calendar::releasedDays(),
    );
}

it('has a problem file for every released day', function () {
    $missing = array_values(array_filter(
        Calendar::releasedDays(),
        fn (array $pair) => ! is_file(Calendar::path(...$pair)),
    ));

    expect($missing)->toBeEmpty();
});

it('has no problem file for an unreleased day', function () {
    $expected = array_map(
        fn (array $pair) => Calendar::path(...$pair),
        Calendar::releasedDays(),
    );

    $actual = glob('source/_problems/*.md') ?: [];

    expect(array_values(array_diff($actual, $expected)))->toBeEmpty();
});

it('gives every problem a year and day matching its filename', function () {
    foreach (problemFiles() as $file) {
        $slug = basename($file->path, '.md');

        expect(Calendar::slug((int) $file->get('year'), (int) $file->get('day')))
            ->toBe($slug);
    }
});

it('stores every answer as a string', function () {
    foreach (problemFiles() as $file) {
        foreach (['answer_one', 'answer_two'] as $key) {
            if (! $file->has($key)) {
                continue;
            }

            expect($file->get($key))->toBeString()
                ->and($file->get($key))->not->toBe('');
        }
    }
});

it('never records part two without part one', function () {
    foreach (problemFiles() as $file) {
        if ($file->has('answer_two')) {
            expect($file->has('answer_one'))->toBeTrue();
        }
    }
});

it('parses every solved_at as a real date', function () {
    foreach (problemFiles() as $file) {
        if (! $file->has('solved_at')) {
            continue;
        }

        $value = $file->get('solved_at');
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $value);

        expect($date)->not->toBeFalse()
            ->and($date->format('Y-m-d'))->toBe((string) $value);
    }
});

it('only records solved_at for a day that has an answer', function () {
    foreach (problemFiles() as $file) {
        if ($file->has('solved_at')) {
            expect($file->has('answer_one'))->toBeTrue();
        }
    }
});

it('keeps notes as flat maps of scalars so they cannot carry raw input', function () {
    $files = problemFiles();

    // Guards against this becoming vacuously true if the glob ever breaks.
    expect($files)->not->toBeEmpty();

    $offenders = [];

    foreach ($files as $file) {
        foreach (['notes_one', 'notes_two'] as $key) {
            if (! $file->has($key)) {
                continue;
            }

            $notes = $file->get($key);

            if (! is_array($notes)) {
                $offenders[] = "{$file->path}:{$key} is not a map";

                continue;
            }

            foreach ($notes as $label => $value) {
                if (! is_string($label) || ! is_scalar($value)) {
                    $offenders[] = "{$file->path}:{$key}.{$label}";
                }
            }
        }
    }

    expect($offenders)->toBeEmpty();
});

it('points every problem at the problem layout', function () {
    foreach (problemFiles() as $file) {
        expect($file->get('extends'))->toBe('_layouts.problem');
    }
});

it('records a title for exactly the days that have been attempted', function () {
    $titled = array_filter(problemFiles(), fn (ProblemFile $f) => $f->has('title'));

    // 23 days have real puzzle content; two of them (2024/07, 2024/08) are
    // started but unanswered, so titles outnumber answered days.
    expect($titled)->toHaveCount(23);
});

it('has a problem file for every ported solution class', function () {
    $missing = [];

    foreach (Runner::all() as [$year, $day]) {
        if (! is_file(Calendar::path($year, $day))) {
            $missing[] = "{$year}/{$day}";
        }
    }

    expect($missing)->toBeEmpty()
        ->and(Runner::all())->toHaveCount(23);
});

it('implements the Solution interface on every ported class', function () {
    foreach (Runner::all() as [$year, $day]) {
        $class = Runner::classFor($year, $day);

        expect(class_exists($class))->toBeTrue()
            ->and(new $class)->toBeInstanceOf(Solution::class);
    }
});

it('records an answer for every day that is not a known unsolved part', function () {
    // Days with a solution class but deliberately no recorded answers, because
    // the original code for them was abandoned mid-debug.
    $unsolved = ['2024-7', '2024-8'];
    $gaps = [];

    foreach (Runner::all() as [$year, $day]) {
        $file = ProblemFile::read(Calendar::path($year, $day));

        if (! $file->has('answer_one') && ! in_array("{$year}-{$day}", $unsolved, true)) {
            $gaps[] = "{$year}/{$day}";
        }
    }

    expect($gaps)->toBeEmpty();
});
