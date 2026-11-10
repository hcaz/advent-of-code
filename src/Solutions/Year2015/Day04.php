<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2015 day 4: The Ideal Stocking Stuffer.
 *
 * Step logic is the original, unchanged. Brute force by design -- the puzzle is
 * a proof of work, and part two runs to nearly four million MD5s, which is why
 * the regression suite is opt-in rather than part of the default run.
 */
final class Day04 implements Solution
{
    private string $secretKey;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $i = 0;
        while (true) {
            $hash = md5($this->secretKey.$i);
            if (str_starts_with($hash, '00000')) {
                return Result::of($i, ['hashes tried' => $i + 1, 'zeroes' => 5]);
            }
            $i++;
        }
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $i = 0;
        while (true) {
            $hash = md5($this->secretKey.$i);
            if (str_starts_with($hash, '000000')) {
                return Result::of($i, ['hashes tried' => $i + 1, 'zeroes' => 6]);
            }
            $i++;
        }
    }

    private function loadData(string $input): void
    {
        $this->secretKey = trim($input);
    }
}
