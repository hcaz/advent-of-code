<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2022 day 4: Camp Cleanup.
 *
 * Step logic is the original, unchanged, including part two's four separate
 * elseif branches rather than a single overlap test.
 */
final class Day04 implements Solution
{
    private Collection $sections;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $overlaps = 0;
        foreach ($this->sections as $section) {
            $one = explode('-', $section['one']);
            $two = explode('-', $section['two']);

            if ($one[0] <= $two[0] && $one[1] >= $two[1]) {
                $overlaps++;
            } elseif ($two[0] <= $one[0] && $two[1] >= $one[1]) {
                $overlaps++;
            }
        }

        return Result::of($overlaps, ['pairs' => $this->sections->count()]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $overlaps = 0;
        foreach ($this->sections as $section) {
            $one = explode('-', $section['one']);
            $two = explode('-', $section['two']);

            if ($one[0] <= $two[0] && $one[1] >= $two[1]) {
                $overlaps++;
            } elseif ($two[0] <= $one[0] && $two[1] >= $one[1]) {
                $overlaps++;
            } elseif ($one[0] <= $two[0] && $one[1] >= $two[0]) {
                $overlaps++;
            } elseif ($two[0] <= $one[0] && $two[1] >= $one[0]) {
                $overlaps++;
            }
        }

        return Result::of($overlaps, ['pairs' => $this->sections->count()]);
    }

    private function loadData(string $input): void
    {
        $this->sections = collect([]);
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                continue;
            }

            $section = explode(',', $line);
            if (count($section) == 2) {
                $this->sections->push(['one' => $section[0], 'two' => $section[1]]);
            }
        }
    }
}
