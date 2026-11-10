<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;
use UnexpectedValueException;

/**
 * 2024 day 6: Guard Gallivant.
 *
 * The original mutated grid state held on the command object and re-parsed the
 * input for every candidate obstruction. That state is local here instead, so
 * each part is independent and re-runnable.
 *
 * Part two is brute force: it re-simulates the whole patrol once per visited
 * cell, which is thousands of simulations of a 130x130 grid. Loop detection is
 * a heuristic -- revisiting any cell more than LOOP_THRESHOLD times -- not a
 * proof. It is preserved as-is because it is what produced the recorded answer;
 * tightening it would be a change in behaviour, not a port.
 */
final class Day06 implements Solution
{
    private const LOOP_THRESHOLD = 10;

    public function partOne(string $input): Result
    {
        [$grid, $startX, $startY] = $this->parse($input);

        $visited = $this->patrol($grid, $startX, $startY);

        return Result::of(count($visited ?? []), [
            'grid' => count($grid).'x'.count($grid[0]),
            'start' => $startX.','.$startY,
        ]);
    }

    public function partTwo(string $input): Result
    {
        [$grid, $startX, $startY] = $this->parse($input);

        $visited = $this->patrol($grid, $startX, $startY) ?? [];
        $loops = 0;

        foreach (array_keys($visited) as $cell) {
            [$x, $y] = array_map('intval', explode('x', (string) $cell));

            // The guard's own starting square cannot hold an obstruction.
            if ($x === $startX && $y === $startY) {
                continue;
            }

            $candidate = $grid;
            $candidate[$y][$x] = '@';

            if ($this->patrol($candidate, $startX, $startY) === null) {
                $loops++;
            }
        }

        return Result::of($loops, [
            'candidates tried' => count($visited),
            'simulations' => count($visited),
        ]);
    }

    /**
     * Walk the patrol until the guard leaves the grid.
     *
     * Returns the visited cells, or null if the guard appears to be looping.
     *
     * @param  list<list<string>>  $grid
     * @return array<string, int>|null
     */
    private function patrol(array $grid, int $x, int $y): ?array
    {
        $height = count($grid);
        $width = count($grid[0]);

        $visited = ["{$x}x{$y}" => 1];

        while ($x > 0 && $x < $width - 1 && $y > 0 && $y < $height - 1) {
            $direction = $grid[$y][$x];
            $grid[$y][$x] = '.';

            $direction = $this->turnUntilClear($grid, $x, $y, $direction);

            match ($direction) {
                '^' => $y--,
                'v' => $y++,
                '<' => $x--,
                '>' => $x++,
                default => throw new UnexpectedValueException(
                    "Unexpected guard direction '{$direction}' at {$x},{$y}",
                ),
            };

            $grid[$y][$x] = $direction;

            $key = "{$x}x{$y}";
            $visited[$key] = ($visited[$key] ?? 0) + 1;

            if ($visited[$key] > self::LOOP_THRESHOLD) {
                return null;
            }
        }

        return $visited;
    }

    /** @param list<list<string>> $grid */
    private function turnUntilClear(array $grid, int $x, int $y, string $direction): string
    {
        $next = match ($direction) {
            '^' => $grid[$y - 1][$x],
            'v' => $grid[$y + 1][$x],
            '<' => $grid[$y][$x - 1],
            '>' => $grid[$y][$x + 1],
            default => throw new UnexpectedValueException(
                "Unexpected guard direction '{$direction}'",
            ),
        };

        if ($next !== '#' && $next !== '@') {
            return $direction;
        }

        // No default arm: the match above has already proved $direction is one
        // of these four, so anything else threw before reaching here.
        return $this->turnUntilClear($grid, $x, $y, match ($direction) {
            '^' => '>',
            'v' => '<',
            '<' => '^',
            '>' => 'v',
        });
    }

    /** @return array{list<list<string>>, int, int} */
    private function parse(string $input): array
    {
        $grid = [];
        $startX = 0;
        $startY = 0;

        foreach (explode("\n", $input) as $line) {
            if ($line === '') {
                continue;
            }

            $row = [];

            for ($i = 0, $len = strlen($line); $i < $len; $i++) {
                $row[] = $line[$i];

                if ($line[$i] === '^') {
                    $startX = $i;
                    $startY = count($grid);
                }
            }

            $grid[] = $row;
        }

        return [$grid, $startX, $startY];
    }
}
