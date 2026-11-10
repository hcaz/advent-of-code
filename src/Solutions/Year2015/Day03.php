<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2015 day 3: Perfectly Spherical Houses in a Vacuum.
 *
 * The input is split with str_split and deliberately NOT trimmed, matching the
 * original. A trailing newline becomes a direction that moves nobody, which
 * cannot change the number of distinct houses, but the faithful reading is
 * kept so the recorded answers are reproducible.
 */
final class Day03 implements Solution
{
    public function partOne(string $input): Result
    {
        $x = 0;
        $y = 0;
        $houses = ['0,0' => 1];

        foreach (str_split($input) as $direction) {
            [$x, $y] = $this->step($direction, $x, $y);
            $houses["{$x},{$y}"] = ($houses["{$x},{$y}"] ?? 0) + 1;
        }

        return Result::of(count($houses), [
            'directions' => strlen($input),
            'most visits' => max($houses),
        ]);
    }

    public function partTwo(string $input): Result
    {
        $positions = [0 => [0, 0], 1 => [0, 0]];
        // Both start on the same house, so it already has two deliveries.
        $houses = ['0,0' => 2];

        foreach (str_split($input) as $index => $direction) {
            $who = $index % 2;
            $positions[$who] = $this->step($direction, ...$positions[$who]);
            [$x, $y] = $positions[$who];
            $houses["{$x},{$y}"] = ($houses["{$x},{$y}"] ?? 0) + 1;
        }

        return Result::of(count($houses), [
            'directions' => strlen($input),
            'most visits' => max($houses),
        ]);
    }

    /** @return array{int, int} */
    private function step(string $direction, int $x, int $y): array
    {
        return match ($direction) {
            '^' => [$x, $y + 1],
            'v' => [$x, $y - 1],
            '>' => [$x + 1, $y],
            '<' => [$x - 1, $y],
            // Anything else (e.g. a trailing newline) moves nobody.
            default => [$x, $y],
        };
    }
}
