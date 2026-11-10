<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2022 day 3: Rucksack Reorganization.
 *
 * Step logic is the original, unchanged -- including feeding a whole string to
 * ord() and testing the whole string for being uppercase, with the original's
 * own comment about why. Only the number_format() before printing is dropped.
 */
final class Day03 implements Solution
{
    private Collection $rucksacks;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $priorityTotal = 0;
        foreach ($this->rucksacks as $rucksack) {
            $itemsInBothCompartments = implode(array_unique(array_intersect(str_split($rucksack['compartment_1']), str_split($rucksack['compartment_2']))));

            // I had hoped I could change 96 to 64 depending on capital or lowercase, but that didn't work
            $number = (ord(strtolower($itemsInBothCompartments)) - 96) + ($itemsInBothCompartments == strtoupper($itemsInBothCompartments) ? 26 : 0);
            if ($number > 0) {
                $priorityTotal = $priorityTotal + $number;
            }
        }

        return Result::of($priorityTotal, ['rucksacks' => $this->rucksacks->count()]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $chunks = $this->rucksacks->chunk(3);

        $priorityTotal = 0;
        foreach ($chunks as $chunk) {
            $elfOneRucksack = $chunk->values()[0]['compartment_1'].$chunk->values()[0]['compartment_2'];
            $elfTwoRucksack = $chunk->values()[1]['compartment_1'].$chunk->values()[1]['compartment_2'];
            $elfThreeRucksack = $chunk->values()[2]['compartment_1'].$chunk->values()[2]['compartment_2'];

            $itemsOnAllElves = implode(array_unique(array_intersect(str_split($elfOneRucksack), str_split($elfTwoRucksack), str_split($elfThreeRucksack))));

            // I had hoped I could change 96 to 64 depending on capital or lowercase, but that didn't work
            $number = (ord(strtolower($itemsOnAllElves)) - 96) + ($itemsOnAllElves == strtoupper($itemsOnAllElves) ? 26 : 0);
            if ($number > 0) {
                $priorityTotal = $priorityTotal + $number;
            }
        }

        return Result::of($priorityTotal, ['groups' => $chunks->count()]);
    }

    private function loadData(string $input): void
    {
        $this->rucksacks = collect([]);
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                continue;
            }

            $first_half = substr($line, 0, strlen($line) / 2);
            $second_half = substr($line, strlen($line) / 2);

            $this->rucksacks->push([
                'compartment_1' => $first_half,
                'compartment_2' => $second_half,
            ]);
        }
    }
}
