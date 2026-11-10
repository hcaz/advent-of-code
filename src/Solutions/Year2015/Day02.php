<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2015 day 2: I Was Told There Would Be No Math.
 *
 * Step logic is the original, unchanged.
 */
final class Day02 implements Solution
{
    private Collection $presents;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $totalFeetOfWrappingPaper = 0;

        foreach ($this->presents as $present) {
            $totalFeetOfWrappingPaper += 2 * $present[0] * $present[1];
            $totalFeetOfWrappingPaper += 2 * $present[1] * $present[2];
            $totalFeetOfWrappingPaper += 2 * $present[2] * $present[0];

            $totalFeetOfWrappingPaper += min($present[0] * $present[1], $present[1] * $present[2], $present[2] * $present[0]);
        }

        return Result::of($totalFeetOfWrappingPaper, ['presents' => $this->presents->count()]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $totalFeetOfRibbon = 0;

        foreach ($this->presents as $present) {
            $totalFeetOfRibbon += 2 * min($present[0] + $present[1], $present[1] + $present[2], $present[2] + $present[0]);
            $totalFeetOfRibbon += $present[0] * $present[1] * $present[2];
        }

        return Result::of($totalFeetOfRibbon, ['presents' => $this->presents->count()]);
    }

    private function loadData(string $input): void
    {
        $this->presents = collect([]);
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                continue;
            }

            $tmpPresents = collect(explode('x', $line));
            $this->presents->push($tmpPresents);
        }
    }
}
