<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2015 day 3: Perfectly Spherical Houses in a Vacuum.
 *
 * Step logic is the original, unchanged -- including str_split on the untrimmed
 * input, the duplicated switch per traveller, and the filter that counts every
 * house rather than only repeat visits (which is in fact the answer the puzzle
 * asks for).
 */
final class Day03 implements Solution
{
    private Collection $directions;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $x = 0;
        $y = 0;
        $houses = ["{$x},{$y}" => 1];

        foreach ($this->directions as $direction) {
            switch ($direction) {
                case '^':
                    $y++;
                    break;
                case 'v':
                    $y--;
                    break;
                case '>':
                    $x++;
                    break;
                case '<':
                    $x--;
                    break;
            }
            if (isset($houses["{$x},{$y}"])) {
                $houses["{$x},{$y}"]++;
            } else {
                $houses["{$x},{$y}"] = 1;
            }
        }

        $houses = collect($houses);

        $housesWithMoreThanOneVisit = $houses->filter(fn ($visits) => $visits >= 1);

        return Result::of($housesWithMoreThanOneVisit->count(), [
            'directions' => $this->directions->count(),
            'houses visited' => $houses->count(),
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $santaX = 0;
        $santaY = 0;
        $robotX = 0;
        $robotY = 0;
        $houses = ["{$santaX},{$santaY}" => 2];

        foreach ($this->directions as $index => $direction) {
            if ($index % 2 === 0) {
                switch ($direction) {
                    case '^':
                        $santaY++;
                        break;
                    case 'v':
                        $santaY--;
                        break;
                    case '>':
                        $santaX++;
                        break;
                    case '<':
                        $santaX--;
                        break;
                }
                if (isset($houses["{$santaX},{$santaY}"])) {
                    $houses["{$santaX},{$santaY}"]++;
                } else {
                    $houses["{$santaX},{$santaY}"] = 1;
                }
            } else {
                switch ($direction) {
                    case '^':
                        $robotY++;
                        break;
                    case 'v':
                        $robotY--;
                        break;
                    case '>':
                        $robotX++;
                        break;
                    case '<':
                        $robotX--;
                        break;
                }
                if (isset($houses["{$robotX},{$robotY}"])) {
                    $houses["{$robotX},{$robotY}"]++;
                } else {
                    $houses["{$robotX},{$robotY}"] = 1;
                }
            }
        }

        $houses = collect($houses);

        $housesWithMoreThanOneVisit = $houses->filter(fn ($visits) => $visits >= 1);

        return Result::of($housesWithMoreThanOneVisit->count(), [
            'directions' => $this->directions->count(),
            'houses visited' => $houses->count(),
        ]);
    }

    private function loadData(string $input): void
    {
        $this->directions = collect(str_split($input));
    }
}
