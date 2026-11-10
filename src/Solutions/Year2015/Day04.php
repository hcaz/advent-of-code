<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2015 day 4: The Ideal Stocking Stuffer.
 *
 * Brute force by design -- the puzzle is a proof-of-work. Part two runs to
 * nearly four million MD5s, which is why the regression suite is opt-in rather
 * than part of the default run.
 */
final class Day04 implements Solution
{
    public function partOne(string $input): Result
    {
        return $this->mine($input, '00000');
    }

    public function partTwo(string $input): Result
    {
        return $this->mine($input, '000000');
    }

    private function mine(string $input, string $prefix): Result
    {
        $key = trim($input);

        for ($i = 0; ; $i++) {
            if (str_starts_with(md5($key.$i), $prefix)) {
                return Result::of($i, [
                    'hashes tried' => $i + 1,
                    'zeroes' => strlen($prefix),
                ]);
            }
        }
    }
}
