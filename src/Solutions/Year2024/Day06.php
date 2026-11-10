<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 6: Guard Gallivant.
 *
 * Step logic is the original, unchanged -- including the mutable grid state,
 * the reload per candidate obstruction in part two, the recursive rotation
 * check, and the "more than ten visits" loop heuristic.
 */
final class Day06 implements Solution
{
    /** @var list<list<string>> */
    private array $data = [];

    private int $guardX = 0;

    private int $guardY = 0;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $positions = $this->runGuardPath();

        return Result::of(count($positions), [
            'grid' => count($this->data).'x'.count($this->data[0]),
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $positions = $this->runGuardPath();

        $possibleObjectLocations = [];

        foreach (array_keys($positions) as $position) {
            $this->loadData($input);

            $position = explode('x', $position);
            $x = $position[0];
            $y = $position[1];

            if ($x == $this->guardX && $y == $this->guardY) {
                continue;
            }

            $this->data[$y][$x] = '@';

            $newPositions = $this->runGuardPath();
            if (is_null($newPositions)) {
                $possibleObjectLocations[] = "{$x}x{$y}";
            }
        }

        return Result::of(count($possibleObjectLocations), [
            'candidates tried' => count($positions),
        ]);
    }

    private function runGuardPath(): ?array
    {
        $positions = [];
        $positions["{$this->guardX}x{$this->guardY}"] = 1;

        while ($this->guardX > 0 && $this->guardX < (count($this->data[0]) - 1) && $this->guardY > 0 && $this->guardY < (count($this->data) - 1)) {
            $direction = $this->data[$this->guardY][$this->guardX];
            $this->data[$this->guardY][$this->guardX] = '.';

            $direction = $this->checkGuardRotation($direction);

            if ($direction === '^') {
                $this->guardY--;
            } elseif ($direction === 'v') {
                $this->guardY++;
            } elseif ($direction === '<') {
                $this->guardX--;
            } elseif ($direction === '>') {
                $this->guardX++;
            }
            $this->data[$this->guardY][$this->guardX] = $direction;

            if (! isset($positions["{$this->guardX}x{$this->guardY}"])) {
                $positions["{$this->guardX}x{$this->guardY}"] = 1;
            } else {
                $positions["{$this->guardX}x{$this->guardY}"]++;

                if ($positions["{$this->guardX}x{$this->guardY}"] > 10) {
                    return null;
                }
            }
        }

        return $positions;
    }

    private function checkGuardRotation($direction): string
    {
        $next = match ($direction) {
            '^' => $this->data[$this->guardY - 1][$this->guardX],
            'v' => $this->data[$this->guardY + 1][$this->guardX],
            '<' => $this->data[$this->guardY][$this->guardX - 1],
            '>' => $this->data[$this->guardY][$this->guardX + 1],
        };

        if ($next == '#' || $next == '@') {
            $direction = match ($direction) {
                '^' => '>',
                'v' => '<',
                '<' => '^',
                '>' => 'v',
            };

            $direction = $this->checkGuardRotation($direction);
        }

        return $direction;
    }

    private function loadData(string $input): void
    {
        $this->data = [];
        $lines = explode("\n", $input);
        foreach ($lines as $lineData) {
            if (empty($lineData)) {
                continue;
            }
            $line = [];
            for ($i = 0; $i < strlen($lineData); $i++) {
                $line[] = $lineData[$i];

                if ($lineData[$i] === '^') {
                    $this->guardX = $i;
                    $this->guardY = count($this->data);
                }
            }
            $this->data[] = $line;
        }
    }
}
