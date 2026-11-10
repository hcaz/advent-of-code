<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 10: Hoof It.
 *
 * Part one counts distinct summits reachable from each trailhead; part two
 * counts distinct routes to them. The walk is the original's: step to a
 * neighbour whose height is the next value up, and at height 9 record the
 * summit -- keyed by position, so part one naturally de-duplicates while part
 * two accumulates a visit count per summit and sums them.
 */
final class Day10 implements Solution
{
    public function partOne(string $input): Result
    {
        return $this->score($input, countRoutes: false);
    }

    public function partTwo(string $input): Result
    {
        return $this->score($input, countRoutes: true);
    }

    private function score(string $input, bool $countRoutes): Result
    {
        $grid = $this->grid($input);
        $total = 0;
        $trailheads = 0;

        foreach ($grid as $row => $line) {
            foreach ($line as $col => $char) {
                if ($char !== '0') {
                    continue;
                }

                $summits = $this->walk($grid, $row, $col, 1, $countRoutes);

                if ($summits === []) {
                    continue;
                }

                $trailheads++;
                $total += $countRoutes ? array_sum($summits) : count($summits);
            }
        }

        return Result::of($total, [
            'grid' => count($grid).'x'.count($grid[0]),
            'trailheads' => $trailheads,
        ]);
    }

    /**
     * Summits reachable from a position, as position => times reached.
     *
     * @param  list<list<string>>  $grid
     * @return array<string, int>
     */
    private function walk(array $grid, int $row, int $col, int $next, bool $countRoutes): array
    {
        $summits = [];

        foreach ([[0, 1], [0, -1], [1, 0], [-1, 0]] as [$dRow, $dCol]) {
            $r = $row + $dRow;
            $c = $col + $dCol;

            if (! isset($grid[$r][$c])) {
                continue;
            }

            $height = (int) $grid[$r][$c];

            if ($height !== $next) {
                continue;
            }

            if ($next === 9) {
                $key = "{$r}-{$c}";
                $summits[$key] = ($summits[$key] ?? 0) + 1;

                continue;
            }

            foreach ($this->walk($grid, $r, $c, $next + 1, $countRoutes) as $key => $count) {
                $summits[$key] = ($summits[$key] ?? 0) + $count;
            }
        }

        return $summits;
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
