<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2023;

use Aoc\PartNotImplemented;
use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2023 day 1: Trebuchet?!
 *
 * Part one's step logic is the original, unchanged.
 *
 * The original's part two only re-found numeric digits by a different route,
 * which is what part one already does; it never handled spelled-out digits,
 * and no answer was recorded. Reporting it as unimplemented rather than
 * publishing a confident wrong number.
 */
final class Day01 implements Solution
{
    private Collection $calibrationLines;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $sumOfCalibrationValues = $this->calibrationLines->map(function ($line) {
            $cleanedLine = preg_replace('/[^0-9]/', '', $line);
            $firstDigit = substr($cleanedLine, 0, 1);
            $lastDigit = substr($cleanedLine, -1);

            return intval("$firstDigit$lastDigit");
        })->sum();

        return Result::of($sumOfCalibrationValues, ['lines' => $this->calibrationLines->count()]);
    }

    public function partTwo(string $input): Result
    {
        throw PartNotImplemented::for(2023, 1, 'two');
    }

    private function loadData(string $input): void
    {
        $this->calibrationLines = collect(explode("\n", $input));
    }
}
