<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/** 2015 day 5: Doesn't He Have Intern-Elves For This? */
final class Day05 implements Solution
{
    public function partOne(string $input): Result
    {
        $strings = $this->strings($input);
        $nice = 0;

        foreach ($strings as $string) {
            if (preg_match('/(ab|cd|pq|xy)/', $string)) {
                continue;
            }

            if (preg_match('/([aeiou].*){3,}/', $string) && preg_match('/(.)\1/', $string)) {
                $nice++;
            }
        }

        return Result::of($nice, ['strings' => count($strings), 'naughty' => count($strings) - $nice]);
    }

    public function partTwo(string $input): Result
    {
        $strings = $this->strings($input);
        $nice = 0;

        foreach ($strings as $string) {
            if (preg_match('/(..).*\1/', $string) && preg_match('/(.).\1/', $string)) {
                $nice++;
            }
        }

        return Result::of($nice, ['strings' => count($strings), 'naughty' => count($strings) - $nice]);
    }

    /** @return list<string> */
    private function strings(string $input): array
    {
        return explode("\n", trim($input));
    }
}
