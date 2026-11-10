<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/** 2015 day 2: I Was Told There Would Be No Math. */
final class Day02 implements Solution
{
    public function partOne(string $input): Result
    {
        $total = 0;

        foreach ($this->presents($input) as [$l, $w, $h]) {
            $sides = [$l * $w, $w * $h, $h * $l];
            $total += 2 * array_sum($sides) + min($sides);
        }

        return Result::of($total, ['presents' => count($this->presents($input))]);
    }

    public function partTwo(string $input): Result
    {
        $total = 0;

        foreach ($this->presents($input) as [$l, $w, $h]) {
            $total += 2 * min($l + $w, $w + $h, $h + $l) + $l * $w * $h;
        }

        return Result::of($total, ['presents' => count($this->presents($input))]);
    }

    /** @return list<array{int, int, int}> */
    private function presents(string $input): array
    {
        $presents = [];

        foreach (explode("\n", $input) as $line) {
            if (trim($line) === '') {
                continue;
            }

            $dimensions = array_map('intval', explode('x', trim($line)));

            if (count($dimensions) === 3) {
                $presents[] = [$dimensions[0], $dimensions[1], $dimensions[2]];
            }
        }

        return $presents;
    }
}
