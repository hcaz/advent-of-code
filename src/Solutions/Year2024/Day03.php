<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/** 2024 day 3: Mull It Over. */
final class Day03 implements Solution
{
    public function partOne(string $input): Result
    {
        [$sum, $count] = $this->sumMuls($input);

        return Result::of($sum, ['instructions' => $count]);
    }

    public function partTwo(string $input): Result
    {
        // The trailing do() closes a final unterminated don't() region.
        $enabled = preg_replace('/don\'t\(\)(.|\n)*?do\(\)/', '', $input.'do()') ?? $input;

        [$sum, $count] = $this->sumMuls($enabled);

        return Result::of($sum, ['instructions' => $count]);
    }

    /** @return array{int, int} */
    private function sumMuls(string $memory): array
    {
        preg_match_all('/mul\((\d{1,3}),(\d{1,3})\)/', $memory, $matches, PREG_SET_ORDER);

        $sum = 0;

        foreach ($matches as $match) {
            $sum += (int) $match[1] * (int) $match[2];
        }

        return [$sum, count($matches)];
    }
}
