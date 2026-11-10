<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2015 day 5: Doesn't He Have Intern-Elves For This?
 *
 * Step logic is the original, unchanged.
 */
final class Day05 implements Solution
{
    private Collection $strings;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $niceStrings = 0;
        foreach ($this->strings as $string) {
            if (preg_match('/(ab|cd|pq|xy)/', $string)) {
                continue;
            }

            if (preg_match('/([aeiou].*){3,}/', $string) && preg_match('/(.)\1/', $string)) {
                $niceStrings++;
            }
        }

        return Result::of($niceStrings, [
            'strings' => $this->strings->count(),
            'naughty' => $this->strings->count() - $niceStrings,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $niceStrings = 0;
        foreach ($this->strings as $string) {
            if (preg_match('/(..).*\1/', $string) && preg_match('/(.).\1/', $string)) {
                $niceStrings++;
            }
        }

        return Result::of($niceStrings, [
            'strings' => $this->strings->count(),
            'naughty' => $this->strings->count() - $niceStrings,
        ]);
    }

    private function loadData(string $input): void
    {
        $this->strings = collect(explode("\n", trim($input)));
    }
}
