<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/** 2024 day 1: Historian Hysteria. */
final class Day01 implements Solution
{
    public function partOne(string $input): Result
    {
        [$left, $right] = $this->lists($input);

        sort($left);
        sort($right);

        $distance = 0;

        foreach ($left as $i => $value) {
            $distance += abs($value - $right[$i]);
        }

        return Result::of($distance, ['pairs' => count($left)]);
    }

    public function partTwo(string $input): Result
    {
        [$left, $right] = $this->lists($input);

        $occurrences = array_count_values($right);
        $similarity = 0;
        $matched = 0;

        foreach ($left as $value) {
            if (isset($occurrences[$value])) {
                $similarity += $value * $occurrences[$value];
                $matched++;
            }
        }

        return Result::of($similarity, [
            'pairs' => count($left),
            'matched' => $matched,
        ]);
    }

    /** @return array{list<int>, list<int>} */
    private function lists(string $input): array
    {
        $left = [];
        $right = [];

        foreach (explode("\n", $input) as $line) {
            $parts = preg_split('/\s+/', trim($line)) ?: [];

            if (count($parts) === 2) {
                $left[] = (int) $parts[0];
                $right[] = (int) $parts[1];
            }
        }

        return [$left, $right];
    }
}
