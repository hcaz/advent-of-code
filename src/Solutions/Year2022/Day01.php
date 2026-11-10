<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 1: Calorie Counting.
 *
 * The original printed its totals through number_format(), so a verbatim port
 * would have produced "66,186" against a recorded "66186".
 */
final class Day01 implements Solution
{
    public function partOne(string $input): Result
    {
        $elves = $this->elfTotals($input);

        return Result::of($elves[0], [
            'elves' => count($elves),
            'runner up' => $elves[1] ?? 0,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $elves = $this->elfTotals($input);
        $top3 = array_slice($elves, 0, 3);

        return Result::of(array_sum($top3), [
            'elves' => count($elves),
            'top three' => implode(', ', $top3),
        ]);
    }

    /**
     * Calorie totals per elf, highest first.
     *
     * A blank line closes the current elf, matching the original. Puzzle inputs
     * end with a newline, so the final elf is closed by the trailing empty
     * element from explode().
     *
     * @return list<int>
     */
    private function elfTotals(string $input): array
    {
        $totals = [];
        $current = 0;
        $hasItems = false;

        foreach (explode("\n", $input) as $line) {
            if (trim($line) === '') {
                if ($hasItems) {
                    $totals[] = $current;
                }
                $current = 0;
                $hasItems = false;

                continue;
            }

            $current += (int) $line;
            $hasItems = true;
        }

        if ($hasItems) {
            $totals[] = $current;
        }

        rsort($totals);

        return $totals;
    }
}
