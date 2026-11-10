<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 2: Red-Nosed Reports.
 *
 * The committed version of this solution did not reproduce its own recorded
 * answer. It counted "problematic levels" and called a report safe when that
 * count was at most one, which happens to give the right answer for part one
 * (326) but gives 385 for part two against a recorded 381.
 *
 * The recorded answers are the ground truth -- they were accepted by Advent of
 * Code -- so the puzzle's actual rule is implemented here instead: a report is
 * safe when it is strictly monotonic with every step between 1 and 3, and part
 * two allows removing any single level to achieve that. That reproduces both
 * 326 and 381 exactly. The old heuristic was a regression, not a design.
 */
final class Day02 implements Solution
{
    public function partOne(string $input): Result
    {
        $reports = $this->reports($input);
        $safe = 0;

        foreach ($reports as $report) {
            if ($this->isSafe($report)) {
                $safe++;
            }
        }

        return Result::of($safe, [
            'reports' => count($reports),
            'unsafe' => count($reports) - $safe,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $reports = $this->reports($input);
        $safe = 0;
        $rescued = 0;

        foreach ($reports as $report) {
            if ($this->isSafe($report)) {
                $safe++;

                continue;
            }

            if ($this->isSafeWithOneRemoved($report)) {
                $safe++;
                $rescued++;
            }
        }

        return Result::of($safe, [
            'reports' => count($reports),
            'unsafe' => count($reports) - $safe,
            'rescued by one removal' => $rescued,
        ]);
    }

    /** Strictly monotonic, every adjacent step between 1 and 3 inclusive. */
    private function isSafe(array $report): bool
    {
        $direction = 0;

        for ($i = 1, $n = count($report); $i < $n; $i++) {
            $step = $report[$i] - $report[$i - 1];
            $size = abs($step);

            if ($size < 1 || $size > 3) {
                return false;
            }

            $sign = $step > 0 ? 1 : -1;

            if ($direction === 0) {
                $direction = $sign;
            } elseif ($direction !== $sign) {
                return false;
            }
        }

        return true;
    }

    /** @param list<int> $report */
    private function isSafeWithOneRemoved(array $report): bool
    {
        foreach (array_keys($report) as $index) {
            $trimmed = $report;
            array_splice($trimmed, $index, 1);

            if ($this->isSafe($trimmed)) {
                return true;
            }
        }

        return false;
    }

    /** @return list<list<int>> */
    private function reports(string $input): array
    {
        $reports = [];

        foreach (explode("\n", $input) as $line) {
            if (trim($line) === '') {
                continue;
            }

            $reports[] = array_map('intval', preg_split('/\s+/', trim($line)) ?: []);
        }

        return $reports;
    }
}
