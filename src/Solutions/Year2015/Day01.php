<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/** 2015 day 1: Not Quite Lisp. */
final class Day01 implements Solution
{
    public function partOne(string $input): Result
    {
        $up = substr_count($input, '(');
        $down = substr_count($input, ')');

        return Result::of($up - $down, [
            'up' => $up,
            'down' => $down,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $floor = 0;
        $length = strlen($input);

        for ($position = 0; $position < $length; $position++) {
            $floor += $input[$position] === '(' ? 1 : -1;

            if ($floor < 0) {
                // Puzzle positions are 1-indexed.
                return Result::of($position + 1, [
                    'instructions' => $length,
                ]);
            }
        }

        return Result::of(0, ['instructions' => $length, 'reached basement' => false]);
    }
}
