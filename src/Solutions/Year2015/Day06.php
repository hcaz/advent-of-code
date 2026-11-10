<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2015 day 6: Probably a Fire Hazard.
 *
 * Step logic is the original, unchanged -- including pre-filling the full
 * 1000x1000 grid with zeroes before processing.
 */
final class Day06 implements Solution
{
    private Collection $instructions;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $lights = [];
        foreach (range(0, 999) as $x) {
            foreach (range(0, 999) as $y) {
                $lights[$x][$y] = 0;
            }
        }

        foreach ($this->instructions as $instruction) {
            preg_match('/(turn on|turn off|toggle) (\d+),(\d+) through (\d+),(\d+)/', $instruction, $matches);
            $action = $matches[1];
            $startX = $matches[2];
            $startY = $matches[3];
            $endX = $matches[4];
            $endY = $matches[5];

            foreach (range($startX, $endX) as $x) {
                foreach (range($startY, $endY) as $y) {
                    switch ($action) {
                        case 'turn on':
                            $lights[$x][$y] = 1;
                            break;
                        case 'turn off':
                            $lights[$x][$y] = 0;
                            break;
                        case 'toggle':
                            $lights[$x][$y] = $lights[$x][$y] === 1 ? 0 : 1;
                            break;
                    }
                }
            }
        }

        return Result::of(collect($lights)->flatten()->sum(), [
            'instructions' => $this->instructions->count(),
            'grid' => '1000x1000',
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $lights = [];
        foreach (range(0, 999) as $x) {
            foreach (range(0, 999) as $y) {
                $lights[$x][$y] = 0;
            }
        }

        foreach ($this->instructions as $instruction) {
            preg_match('/(turn on|turn off|toggle) (\d+),(\d+) through (\d+),(\d+)/', $instruction, $matches);
            $action = $matches[1];
            $startX = $matches[2];
            $startY = $matches[3];
            $endX = $matches[4];
            $endY = $matches[5];

            foreach (range($startX, $endX) as $x) {
                foreach (range($startY, $endY) as $y) {
                    switch ($action) {
                        case 'turn on':
                            $lights[$x][$y]++;
                            break;
                        case 'turn off':
                            if ($lights[$x][$y] > 0) {
                                $lights[$x][$y]--;
                            }
                            break;
                        case 'toggle':
                            $lights[$x][$y] += 2;
                            break;
                    }
                }
            }
        }

        return Result::of(collect($lights)->flatten()->sum(), [
            'instructions' => $this->instructions->count(),
            'grid' => '1000x1000',
        ]);
    }

    private function loadData(string $input): void
    {
        $this->instructions = collect(explode("\n", trim($input)));
    }
}
