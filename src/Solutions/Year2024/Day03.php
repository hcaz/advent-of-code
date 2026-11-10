<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 3: Mull It Over.
 *
 * Step logic is the original, unchanged.
 */
final class Day03 implements Solution
{
    private string $memory;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        [$count, $instructions] = $this->calculateMuls($this->memory);

        return Result::of($count, ['instructions' => $instructions]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        // / Add a trailing do() to ensure the last don't() is removed
        $cleanedMemory = preg_replace('/don\'t\(\)(.|\n)*?do\(\)/', '', $this->memory.'do()');
        [$count, $instructions] = $this->calculateMuls($cleanedMemory);

        return Result::of($count, ['instructions' => $instructions]);
    }

    /** @return array{int, int} */
    private function calculateMuls($memory): array
    {
        preg_match_all("/mul\(\d{1,3},\d{1,3}\)/", $memory, $matches);

        $count = 0;
        foreach ($matches[0] as $match) {
            $numbers = explode(',', str_replace(['mul(', ')'], '', $match));
            $count += $numbers[0] * $numbers[1];
        }

        return [$count, count($matches[0])];
    }

    private function loadData(string $input): void
    {
        $this->memory = $input;
    }
}
