<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;
use UnexpectedValueException;

/**
 * 2015 day 6: Probably a Fire Hazard.
 *
 * The original pre-filled a 1000x1000 array with zeroes before processing, a
 * million writes per part. Unset cells are treated as zero here instead, which
 * is equivalent -- the answer is the sum of all cells -- and much faster.
 */
final class Day06 implements Solution
{
    public function partOne(string $input): Result
    {
        $lights = [];

        foreach ($this->instructions($input) as [$action, $startX, $startY, $endX, $endY]) {
            for ($x = $startX; $x <= $endX; $x++) {
                for ($y = $startY; $y <= $endY; $y++) {
                    $lights[$x][$y] = match ($action) {
                        'turn on' => 1,
                        'turn off' => 0,
                        'toggle' => ($lights[$x][$y] ?? 0) === 1 ? 0 : 1,
                        default => throw new UnexpectedValueException("Unknown action '{$action}'"),
                    };
                }
            }
        }

        $on = 0;
        foreach ($lights as $column) {
            $on += array_sum($column);
        }

        return Result::of($on, [
            'instructions' => count($this->instructions($input)),
            'grid' => '1000x1000',
        ]);
    }

    public function partTwo(string $input): Result
    {
        $lights = [];

        foreach ($this->instructions($input) as [$action, $startX, $startY, $endX, $endY]) {
            for ($x = $startX; $x <= $endX; $x++) {
                for ($y = $startY; $y <= $endY; $y++) {
                    $current = $lights[$x][$y] ?? 0;

                    $lights[$x][$y] = match ($action) {
                        'turn on' => $current + 1,
                        'turn off' => max(0, $current - 1),
                        'toggle' => $current + 2,
                        default => throw new UnexpectedValueException("Unknown action '{$action}'"),
                    };
                }
            }
        }

        $brightness = 0;
        foreach ($lights as $column) {
            $brightness += array_sum($column);
        }

        return Result::of($brightness, [
            'instructions' => count($this->instructions($input)),
            'grid' => '1000x1000',
        ]);
    }

    /** @return list<array{string, int, int, int, int}> */
    private function instructions(string $input): array
    {
        $instructions = [];

        foreach (explode("\n", trim($input)) as $line) {
            if (preg_match('/(turn on|turn off|toggle) (\d+),(\d+) through (\d+),(\d+)/', $line, $m)) {
                $instructions[] = [$m[1], (int) $m[2], (int) $m[3], (int) $m[4], (int) $m[5]];
            }
        }

        return $instructions;
    }
}
