<?php

declare(strict_types=1);

use Aoc\Calendar;
use Aoc\Data;
use Aoc\ProblemFile;
use Aoc\Runner;

/**
 * Every ported solution must still produce the answer recorded in its problem
 * file. This is the regression gate for the port.
 *
 * Excluded from the default suite (see the <groups> block in phpunit.xml)
 * because it needs decrypted inputs, which CI deliberately does not have, and
 * because some days are brute force and take seconds. Run it locally with:
 *
 *   ./vendor/bin/pest --group=solutions
 */
beforeEach(function () {
    $data = Data::unlocked();

    if ($data->isLocked()) {
        $this->markTestSkipped('data/ is encrypted; run git-crypt unlock.');
    }
});

it('reproduces every recorded answer', function () {
    $runner = new Runner(Data::unlocked());
    $checked = 0;
    $failures = [];

    foreach (Runner::all() as [$year, $day]) {
        $file = ProblemFile::read(Calendar::path($year, $day));

        foreach (['one', 'two'] as $part) {
            $expected = $file->get('answer_'.$part);

            if ($expected === null) {
                continue;
            }

            $result = $runner->run($year, $day, $part);

            if ($result === null) {
                $failures[] = "{$year}/{$day} part {$part}: no input available";

                continue;
            }

            $checked++;

            if ($result->answer !== (string) $expected) {
                $failures[] = "{$year}/{$day} part {$part}: got {$result->answer}, recorded {$expected}";
            }
        }
    }

    expect($failures)->toBeEmpty()
        ->and($checked)->toBeGreaterThan(0);
})->group('solutions');

it('has a problem file for every ported solution', function () {
    foreach (Runner::all() as [$year, $day]) {
        expect(Calendar::path($year, $day))->toBeFile();
    }
})->group('solutions');
