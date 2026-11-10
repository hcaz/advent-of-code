<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;

/** 2022 day 4: Camp Cleanup. */
final class Day04 implements Solution
{
    public function partOne(string $input): Result
    {
        $pairs = $this->pairs($input);
        $contained = 0;

        foreach ($pairs as [$a, $b]) {
            if ($this->contains($a, $b) || $this->contains($b, $a)) {
                $contained++;
            }
        }

        return Result::of($contained, ['pairs' => count($pairs)]);
    }

    public function partTwo(string $input): Result
    {
        $pairs = $this->pairs($input);
        $overlapping = 0;

        foreach ($pairs as [$a, $b]) {
            // Any shared section at all, which includes full containment.
            if ($a[0] <= $b[1] && $b[0] <= $a[1]) {
                $overlapping++;
            }
        }

        return Result::of($overlapping, ['pairs' => count($pairs)]);
    }

    /** @param array{int, int} $outer */
    private function contains(array $outer, array $inner): bool
    {
        return $outer[0] <= $inner[0] && $outer[1] >= $inner[1];
    }

    /** @return list<array{array{int, int}, array{int, int}}> */
    private function pairs(string $input): array
    {
        $pairs = [];

        foreach (explode("\n", $input) as $line) {
            $sections = explode(',', trim($line));

            if (count($sections) !== 2) {
                continue;
            }

            $one = array_map('intval', explode('-', $sections[0]));
            $two = array_map('intval', explode('-', $sections[1]));

            $pairs[] = [[$one[0], $one[1]], [$two[0], $two[1]]];
        }

        return $pairs;
    }
}
