<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 3: Rucksack Reorganization.
 *
 * The original's priority calculation is preserved exactly, including the fact
 * that it feeds a whole string into ord() (which only reads the first byte) and
 * tests the whole string for being uppercase. With one shared item per line
 * that is equivalent to the per-character version, and it is what produced the
 * recorded answers.
 *
 * number_format() on the total has been dropped -- see Day01.
 */
final class Day03 implements Solution
{
    public function partOne(string $input): Result
    {
        $lines = $this->lines($input);
        $total = 0;

        foreach ($lines as $line) {
            $half = intdiv(strlen($line), 2);
            $shared = implode(array_unique(array_intersect(
                str_split(substr($line, 0, $half)),
                str_split(substr($line, $half)),
            )));

            $total += $this->priority($shared);
        }

        return Result::of($total, ['rucksacks' => count($lines)]);
    }

    public function partTwo(string $input): Result
    {
        $lines = $this->lines($input);
        $total = 0;
        $groups = array_chunk($lines, 3);

        foreach ($groups as $group) {
            if (count($group) < 3) {
                continue;
            }

            $shared = implode(array_unique(array_intersect(
                str_split($group[0]),
                str_split($group[1]),
                str_split($group[2]),
            )));

            $total += $this->priority($shared);
        }

        return Result::of($total, ['groups' => count($groups)]);
    }

    /** a-z => 1-26, A-Z => 27-52. */
    private function priority(string $item): int
    {
        if ($item === '') {
            return 0;
        }

        $value = (ord(strtolower($item)) - 96) + ($item === strtoupper($item) ? 26 : 0);

        return $value > 0 ? $value : 0;
    }

    /** @return list<string> */
    private function lines(string $input): array
    {
        return array_values(array_filter(
            array_map('trim', explode("\n", $input)),
            fn (string $line) => $line !== '',
        ));
    }
}
