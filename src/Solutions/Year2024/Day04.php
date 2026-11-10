<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 4: Ceres Search.
 *
 * The original spelled out all eight directions for part one and all four
 * diagonal arrangements for part two as separate if blocks. Both collapse to a
 * loop here. Part two's four cases are exactly the combinations of each
 * diagonal reading M-A-S in either direction, so the condition below is
 * equivalent.
 */
final class Day04 implements Solution
{
    private const WORD = 'XMAS';

    /** The eight directions, as [dRow, dCol]. */
    private const DIRECTIONS = [
        [0, 1], [0, -1], [1, 0], [-1, 0],
        [1, 1], [1, -1], [-1, 1], [-1, -1],
    ];

    public function partOne(string $input): Result
    {
        $grid = $this->grid($input);
        $rows = count($grid);
        $cols = count($grid[0]);
        $length = strlen(self::WORD);
        $found = 0;

        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                if ($grid[$row][$col] !== self::WORD[0]) {
                    continue;
                }

                foreach (self::DIRECTIONS as [$dRow, $dCol]) {
                    $endRow = $row + $dRow * ($length - 1);
                    $endCol = $col + $dCol * ($length - 1);

                    if ($endRow < 0 || $endRow >= $rows || $endCol < 0 || $endCol >= $cols) {
                        continue;
                    }

                    for ($i = 1; $i < $length; $i++) {
                        if ($grid[$row + $dRow * $i][$col + $dCol * $i] !== self::WORD[$i]) {
                            continue 2;
                        }
                    }

                    $found++;
                }
            }
        }

        return Result::of($found, ['grid' => $rows.'x'.$cols]);
    }

    public function partTwo(string $input): Result
    {
        $grid = $this->grid($input);
        $rows = count($grid);
        $cols = count($grid[0]);
        $found = 0;

        for ($row = 1; $row < $rows - 1; $row++) {
            for ($col = 1; $col < $cols - 1; $col++) {
                if ($grid[$row][$col] !== 'A') {
                    continue;
                }

                if ($this->isMas($grid[$row - 1][$col - 1], $grid[$row + 1][$col + 1])
                    && $this->isMas($grid[$row + 1][$col - 1], $grid[$row - 1][$col + 1])) {
                    $found++;
                }
            }
        }

        return Result::of($found, ['grid' => $rows.'x'.$cols]);
    }

    /** One diagonal of the X, in either direction. */
    private function isMas(string $a, string $b): bool
    {
        return ($a === 'M' && $b === 'S') || ($a === 'S' && $b === 'M');
    }

    /** @return list<list<string>> */
    private function grid(string $input): array
    {
        $grid = [];

        foreach (explode("\n", $input) as $line) {
            if ($line === '') {
                continue;
            }

            $grid[] = str_split(rtrim($line, "\r"));
        }

        return $grid;
    }
}
